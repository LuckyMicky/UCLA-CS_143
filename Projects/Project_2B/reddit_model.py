from __future__ import print_function
from pyspark import SparkConf, SparkContext
from pyspark.sql import SQLContext

# IMPORT OTHER MODULES HERE
import cleantext
from pyspark.sql.types import ArrayType, StringType
from pyspark.sql.functions import udf, to_date, avg
from pyspark.ml.feature import CountVectorizer
from pyspark.ml.classification import LogisticRegression
from pyspark.ml.tuning import CrossValidator, ParamGridBuilder
from pyspark.ml.evaluation import BinaryClassificationEvaluator
# for loading models only:
# from pyspark.ml.tuning import CrossValidatorModel
# from pyspark.ml.feature import CountVectorizerModel


def concat_string_array(arr):
    r = []
    for i in range(1, 4):
        r += arr[i].split()
    return r


def main(sqlContext):
    """Main function takes a Spark SQL context."""
    # YOUR CODE HERE

    comments = sqlContext.read.json("comments-minimal.json.bz2")
    submissions = sqlContext.read.json("submissions.json.bz2")
    label = sqlContext.read.csv('labeled_data.csv', header=True, inferSchema=True)
    # comments.write.parquet("comments.parquet")
    # submissions.write.parquet("submissions.parquet")
    # label.write.parquet("label.parquet")
    # comments = sqlContext.read.parquet("comments.parquet")
    # comments.createOrReplaceTempView("comments")
    # submissions = sqlContext.read.parquet("submissions.parquet")
    # submissions.createOrReplaceTempView("submissions")
    # label = sqlContext.read.parquet("label.parquet")
    # label.createOrReplaceTempView("label")

    # task 2
    df2 = sqlContext.sql(
        '''SELECT DISTINCT(label.Input_id),comments.*, label.labeldem, label.labelgop, label.labeldjt FROM label INNER JOIN comments ON label.Input_id=comments.id ''')

    # Question 1:
    # Input_id -> (labeldem, labelgop, labeldjt)

    # Question 2:
    # The data frame is not normalized, with redundant data. Given a comment id, we can uniquely identify the body of
    # the comment. Given the author URL, we can find the can_gild status. Given author URL and subreddit_id, we can find
    # author flair text and its css class string. We can decompose it into 4NF after identifying some functional
    # dependencies. We believe the collector of the data stored it in this way because it is easier to update one table
    # at a time than to update multiple ones. Also, a user status might change in future time, so the collector collected
    # snapshot of the comments with the most current information.

    # task 4 and 5
    sanitize_udf = udf(cleantext.sanitize, ArrayType(StringType()))
    df4 = df2.withColumn('sanitized', sanitize_udf('body'))
    concat_udf = udf(concat_string_array, ArrayType(StringType()))
    df5 = df4.withColumn('ngram', concat_udf('sanitized'))
    df5 = df5.drop('sanitized')  # 1779
    # df5.write.parquet("df5.parquet")

    # task 6A
    # df5 = sqlContext.read.parquet("df5.parquet")
    cv = CountVectorizer(inputCol="ngram", outputCol="features", minDF=6)
    model = cv.fit(df5)
    # model.save('cv.model')
    result = model.transform(df5)
    # result.show(truncate=False)

    # task 6B
    result.createOrReplaceTempView("result")
    df6_positive = sqlContext.sql("SELECT *, IF(labeldjt==1,1,0) AS label FROM result")
    df6_negative = sqlContext.sql("SELECT *, IF(labeldjt==-1,1,0) AS label FROM result")

    # task 7
    # Initialize two logistic regression models.
    # Replace labelCol with the column containing the label, and featuresCol with the column containing the features.
    poslr = LogisticRegression(labelCol="label", featuresCol="features",
                               maxIter=10).setThreshold(0.2)
    neglr = LogisticRegression(labelCol="label", featuresCol="features",
                               maxIter=10).setThreshold(0.4)
    # This is a binary classifier so we need an evaluator that knows how to deal with binary classifiers.
    posEvaluator = BinaryClassificationEvaluator()
    negEvaluator = BinaryClassificationEvaluator()
    # There are a few parameters associated with logistic regression. We do not know what they are a priori.
    # We do a grid search to find the best parameters. We can replace [1.0] with a list of values to try.
    # We will assume the parameter is 1.0. Grid search takes forever.
    posParamGrid = ParamGridBuilder().addGrid(poslr.regParam, [1.0]).build()
    negParamGrid = ParamGridBuilder().addGrid(neglr.regParam, [1.0]).build()
    # We initialize a 5 fold cross-validation pipeline.
    posCrossval = CrossValidator(
        estimator=poslr,
        evaluator=posEvaluator,
        estimatorParamMaps=posParamGrid,
        numFolds=5)
    negCrossval = CrossValidator(
        estimator=neglr,
        evaluator=negEvaluator,
        estimatorParamMaps=negParamGrid,
        numFolds=5)
    # Although crossvalidation creates its own train/test sets for
    # tuning, we still need a labeled test set, because it is not
    # accessible from the crossvalidator (argh!)
    # Split the data 50/50
    posTrain, posTest = df6_positive.randomSplit([0.5, 0.5])
    negTrain, negTest = df6_negative.randomSplit([0.5, 0.5])
    # Train the models
    print("Training positive classifier...")
    posModel = posCrossval.fit(posTrain)
    print("Training negative classifier...")
    negModel = negCrossval.fit(negTrain)

    # Once we train the models, we don't want to do it again. We can save the models and load them again later.
    # posModel.save("pos.model")
    # negModel.save("neg.model")

    # task 8
    ss = submissions.sample(False, 0.2, None)
    df8 = comments.join(ss, comments.link_id.substr(4, 12) == ss.id).select(
        comments.created_utc, comments.score.alias('cscore'), ss.score.alias('sscore'), ss.title,
        ss.locked, ss.over_18, comments.author_flair_text, comments.id, comments.body)

    # Question 3
    # df8.explain()
    # == Physical Plan ==
    # *(2) Project [created_utc#10L, score#20L AS cscore#1619L, score#92L AS sscore#1620L, title#106, locked#76, over_18#83, author_flair_text#3, id#14, body#4]
    # +- *(2) BroadcastHashJoin [substring(link_id#16, 4, 12)], [id#69], Inner, BuildRight
    #    :- *(2) Project [author_flair_text#3, body#4, created_utc#10L, id#14, link_id#16, score#20L]
    #    :  +- *(2) Filter isnotnull(link_id#16)
    #    :     +- *(2) FileScan parquet [author_flair_text#3,body#4,created_utc#10L,id#14,link_id#16,score#20L] Batched: true, Format: Parquet, Location: InMemoryFileIndex[file:/media/sf_vm-shared/comments.parquet], PartitionFilters: [], PushedFilters: [IsNotNull(link_id)], ReadSchema: struct<author_flair_text:string,body:string,created_utc:bigint,id:string,link_id:string,score:big...
    #    +- BroadcastExchange HashedRelationBroadcastMode(List(input[0, string, false]))
    #       +- *(1) Filter isnotnull(id#69)
    #          +- *(1) Sample 0.0, 0.2, false, 1565934737914995123
    #             +- *(1) FileScan parquet [id#69,locked#76,over_18#83,score#92L,title#106] Batched: true, Format: Parquet, Location: InMemoryFileIndex[file:/media/sf_vm-shared/submissions.parquet], PartitionFilters: [], PushedFilters: [], ReadSchema: struct<id:string,locked:boolean,over_18:boolean,score:bigint,title:string>

    # df8 = comments.join(submissions, comments.link_id.substr(4, 12) == submissions.id)\
    #     .select(comments.created_utc, comments.score.alias('cscore'), submissions.score.alias('sscore'), submissions.title, comments.author_flair_text, comments.id, comments.body)
    # df8.write.parquet("df8.parquet")

    # task 9
    # model = CountVectorizerModel.load('cv.model')
    # posModel = CrossValidatorModel.load("pos.model")
    # negModel = CrossValidatorModel.load("neg.model")
    df92 = df8.filter("body NOT LIKE '%/s%'").filter("body NOT LIKE '&gt%'")
    df94 = df92.withColumn('sanitized', sanitize_udf('body'))
    df95 = df94.withColumn('ngram', concat_udf('sanitized'))
    df95 = df95.drop('sanitized')
    df9 = model.transform(df95)
    df9pos = posModel.transform(df9).withColumnRenamed(
        'prediction', 'Positive').drop('rawPrediction', 'probability', 'ngram')
    df10 = negModel.transform(df9pos).withColumnRenamed(
        'prediction', 'Negative').drop('rawPrediction', 'probability', 'features')
    # df10.write.parquet("df10.parquet")
    # df9.write.parquet("df9.parquet")
    # posResult.write.parquet("df9pos.parquet")
    # negResult.write.parquet("df9neg.parquet")

    # task 10
    # df9 = sqlContext.read.parquet("df9.parquet")
    # posResult = sqlContext.read.parquet("df9pos.parquet")
    # negResult = sqlContext.read.parquet("df9neg.parquet")
    states = ['Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California',
              'Colorado', 'Connecticut', 'Delaware', 'District of Columbia', 'Florida',
              'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas',
              'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan',
              'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada',
              'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina',
              'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island',
              'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont',
              'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming']

    # 1
    df10.groupBy().avg('Positive', 'Negative').show()
    # +------------------+------------------+
    # |     avg(Positive)|     avg(Negative)|
    # +------------------+------------------+
    # |0.3285865077437555|0.4712543825234617|
    # +------------------+------------------+


    # 2
    byDate = df10.select(to_date(df10.created_utc.cast('timestamp')).alias(
        'date'), df10.Positive, df10.Negative).groupBy('date').avg('Positive', 'Negative')
    byDate.repartition(1).write.format("com.databricks.spark.csv").option(
        "header", "true").save('time_data.csv')
    # +----------+-------------------+
    # |      date|    avg(prediction)|
    # +----------+-------------------+
    # |2017-08-11| 0.3432203389830508|
    # |2017-09-11| 0.6041666666666666|
    # |2017-01-06|0.45698166431593795|
    # |2017-02-26| 0.2857142857142857|
    # |2017-01-27| 0.4057971014492754|
    # |2017-09-28|              0.495|
    # |2016-12-19|0.31800766283524906|
    # |2016-11-08| 0.3527644230769231|
    # |2017-01-24| 0.4975514201762977|
    # |2017-06-29| 0.3684210526315789|
    # |2017-09-29| 0.4827586206896552|
    # |2017-07-31| 0.4574898785425101|
    # |2017-02-16| 0.4444444444444444|
    # |2017-08-18| 0.4482758620689655|
    # |2017-12-02|0.37116564417177916|
    # |2017-08-14| 0.3838383838383838|
    # |2017-10-23| 0.4175824175824176|
    # |2017-12-25|0.44907407407407407|
    # |2017-04-09| 0.3409090909090909|
    # |2017-03-28| 0.4363143631436314|
    # +----------+-------------------+

    # 3
    byState = df10[df10.author_flair_text.isin(
        states)].groupBy('author_flair_text').avg('Positive', 'Negative')
    byState.repartition(1).write.format("com.databricks.spark.csv").option(
        "header", "true").save('state_data.csv')
    # +-----------------+-------------------+
    # |author_flair_text|    avg(prediction)|
    # +-----------------+-------------------+
    # |             Utah|0.37254901960784315|
    # |           Hawaii|0.42857142857142855|
    # |        Minnesota| 0.3856893542757417|
    # |             Ohio|  0.410427807486631|
    # |           Oregon| 0.4177831912302071|
    # |         Arkansas| 0.3548387096774194|
    # |            Texas|         0.43359375|
    # |     North Dakota| 0.4126984126984127|
    # |     Pennsylvania|0.42705882352941177|
    # |      Connecticut|0.40119760479041916|
    # |          Vermont|0.38028169014084506|
    # |         Nebraska| 0.4528301886792453|
    # |           Nevada| 0.4110429447852761|
    # |       Washington|  0.407436096049574|
    # |         Illinois|0.44341801385681295|
    # |         Oklahoma|               0.43|
    # |         Delaware|                0.4|
    # |           Alaska| 0.4423076923076923|
    # |       New Mexico| 0.5076923076923077|
    # |    West Virginia|                0.5|
    # +-----------------+-------------------+

    # 4
    byCommentScore = df10.groupBy('cscore').avg('Positive', 'Negative')
    byStoryScore = df10.groupBy('sscore').avg('Positive', 'Negative')
    byCommentScore.repartition(1).write.format("com.databricks.spark.csv").option(
        "header", "true").save('comment_score.csv')
    byStoryScore.repartition(1).write.format("com.databricks.spark.csv").option(
        "header", "true").save('story_score.csv')

    # 5 Locked vs Unlocked
    byDateLocked = df10.filter('locked == true').select(to_date(df10.created_utc.cast('timestamp')).alias('date'), df10.Positive, df10.Negative).groupBy('date').avg('Positive', 'Negative')
    byDateUnlocked = df10.filter('locked == false').select(to_date(df10.created_utc.cast(
        'timestamp')).alias('date'), df10.Positive, df10.Negative).groupBy('date').avg('Positive', 'Negative')
    byDateLocked.repartition(1).write.format("com.databricks.spark.csv").option(
        "header", "true").save('locked_data.csv')
    byDateUnlocked.repartition(1).write.format("com.databricks.spark.csv").option(
        "header", "true").save('unlocked_data.csv')

    # 5 over_18
    byDate18 = df10.filter('over_18 == true').select(to_date(df10.created_utc.cast(
        'timestamp')).alias('date'), df10.Positive, df10.Negative).groupBy('date').avg('Positive', 'Negative')
    byDate18.repartition(1).write.format("com.databricks.spark.csv").option(
        "header", "true").save('over18_data.csv')

    # final 4
    dff4 = df10.groupBy('title').agg(avg('Positive').alias('avgPos'), avg('Negative').alias('avgNeg'))
    dff4.orderBy('avgPos', ascending=0).limit(10).show(truncate=False)
    dff4.orderBy('avgNeg', ascending=0).limit(10).show(truncate=False)


if __name__ == "__main__":
    conf = SparkConf().setAppName("CS143 Project 2B")
    conf = conf.setMaster("local[*]")
    # conf = (conf.set('spark.executor.memory', '4G').set('spark.driver.memory', '4G'))
    sc = SparkContext(conf=conf)
    sqlContext = SQLContext(sc)
    sc.addPyFile("cleantext.py")
    main(sqlContext)
