#!/usr/bin/env python

"""Clean comment text for easier parsing."""

from __future__ import print_function

import re
import string
import argparse
import json


__author__ = ""
__email__ = ""

# Some useful data.
_CONTRACTIONS = {
    "tis": "'tis",
    "aint": "ain't",
    "amnt": "amn't",
    "arent": "aren't",
    "cant": "can't",
    "couldve": "could've",
    "couldnt": "couldn't",
    "didnt": "didn't",
    "doesnt": "doesn't",
    "dont": "don't",
    "hadnt": "hadn't",
    "hasnt": "hasn't",
    "havent": "haven't",
    "hed": "he'd",
    "hell": "he'll",
    "hes": "he's",
    "howd": "how'd",
    "howll": "how'll",
    "hows": "how's",
    "id": "i'd",
    "ill": "i'll",
    "im": "i'm",
    "ive": "i've",
    "isnt": "isn't",
    "itd": "it'd",
    "itll": "it'll",
    "its": "it's",
    "mightnt": "mightn't",
    "mightve": "might've",
    "mustnt": "mustn't",
    "mustve": "must've",
    "neednt": "needn't",
    "oclock": "o'clock",
    "ol": "'ol",
    "oughtnt": "oughtn't",
    "shant": "shan't",
    "shed": "she'd",
    "shell": "she'll",
    "shes": "she's",
    "shouldve": "should've",
    "shouldnt": "shouldn't",
    "somebodys": "somebody's",
    "someones": "someone's",
    "somethings": "something's",
    "thatll": "that'll",
    "thats": "that's",
    "thatd": "that'd",
    "thered": "there'd",
    "therere": "there're",
    "theres": "there's",
    "theyd": "they'd",
    "theyll": "they'll",
    "theyre": "they're",
    "theyve": "they've",
    "wasnt": "wasn't",
    "wed": "we'd",
    "wedve": "wed've",
    "well": "we'll",
    "were": "we're",
    "weve": "we've",
    "werent": "weren't",
    "whatd": "what'd",
    "whatll": "what'll",
    "whatre": "what're",
    "whats": "what's",
    "whatve": "what've",
    "whens": "when's",
    "whered": "where'd",
    "wheres": "where's",
    "whereve": "where've",
    "whod": "who'd",
    "whodve": "whod've",
    "wholl": "who'll",
    "whore": "who're",
    "whos": "who's",
    "whove": "who've",
    "whyd": "why'd",
    "whyre": "why're",
    "whys": "why's",
    "wont": "won't",
    "wouldve": "would've",
    "wouldnt": "wouldn't",
    "yall": "y'all",
    "youd": "you'd",
    "youll": "you'll",
    "youre": "you're",
    "youve": "you've"
}

# You may need to write regular expressions.


def unigram(text):
    delimiters = " .", " ,", " !", " ?", " :", " ;"
    regexPattern = '|'.join(map(re.escape, delimiters))
    punc_less = re.split(regexPattern, text)
    result = ""
    for j in punc_less:
        if len(j) > 0 and j != " ":
            if j[-1] == " ":
                j = j[:-1]
            if j[0] == " ":
                j = j[1:]
            result += j + " "
    result = result[:-1]
    return result


def bigram(text):
    delimiters = " .", " ,", " !", " ?", " :", " ;"
    regexPattern = '|'.join(map(re.escape, delimiters))
    punc_less = re.split(regexPattern, text)
    j = 0
    result = ""
    while j < len(punc_less):
        split_text = punc_less[j].split(" ")
        j += 1
        if len(split_text) > 1:
            if split_text[-1] == "":
                split_text = split_text[:-1]
            # print(split_text)
            if split_text[0] == "":
                split_text = split_text[1:]
            # print(split_text)
            i = 0
            while i < len(split_text) - 1:
                result = result + split_text[i] + "_" + split_text[i + 1] + " "
                i = i + 1

    result = result[:-1]
    return result


def trigram(text):
    delimiters = " .", " ,", " !", " ?", " :", " ;"
    regexPattern = '|'.join(map(re.escape, delimiters))
    punc_less = re.split(regexPattern, text)
    j = 0
    result = ""
    while j < len(punc_less):
        split_text = punc_less[j].split(" ")
        j += 1
        if len(split_text) > 2:
            if split_text[-1] == "":
                split_text = split_text[:-1]
            if split_text[0] == "":
                split_text = split_text[1:]
            i = 0
            while i < len(split_text) - 2:
                result = result + split_text[i] + "_" + \
                    split_text[i + 1] + "_" + split_text[i + 2] + " "
                i = i + 1
    result = result[:-1]
    return result


def sanitize(text):
    """Do parse the text in variable "text" according to the spec, and return
    a LIST containing FOUR strings
    1. The parsed text.
    2. The unigrams
    3. The bigrams
    4. The trigrams
    """

    # YOUR CODE GOES BELOW:
    single_space = re.sub("[\t\n]", " ", text)  # 1
    no_url = re.sub(r'http\S+', '', single_space)  # 2
    lower_case = no_url.lower()  # 6
    punc = string.punctuation
    punc_pattern = r'([{}])'.format(punc)
    remove_pattern = r'[^ a-z0-9!?,.;:]'
    temp = re.sub(r'^[^a-z0-9]*', r'', lower_case)
    # print(temp)
    temp = re.sub(r'(?<=[ a-z0-9!?,.;:])' + remove_pattern + r'*(?=[^ a-z0-9])' +
                  r'|(?<=[^a-z0-9!?,.;:])' + remove_pattern + r'*(?=[ a-z0-9])',
                  r'', temp)
    # print(temp)
    temp = re.sub(r'(?<=[ a-z0-9!?,.;:])' + remove_pattern + r'*(?=[ ])' +
                  r'|(?<=[ ])' + remove_pattern + r'*(?=[ a-z0-9!?,.;:])',
                  r'', temp)
    # print(temp)
    temp = re.sub(r'[^a-z0-9!?,.;:]*$', r'', temp)
    # print(temp)
    temp = re.sub(r'(?<=[a-z0-9!?,.;:])' + punc_pattern + r'(?=[^a-z0-9])' +
                  r'|(?<=[^a-z0-9])' + punc_pattern + r'(?=[a-z0-9!?,.;:])',
                  r' \g<1> ', temp)
    # print(temp)
    temp = re.sub(r'(?<=[a-z0-9!?,.;:])' + punc_pattern + r'$', r' \g<1> ', temp)
    # print(temp)
    multiple_space = re.sub("[ ]+", " ", temp)  # 3
    parsed_text = re.sub(r' $', r'', multiple_space)  # replace ending white space
    unigrams = unigram(multiple_space)
    bigrams = bigram(multiple_space)
    trigrams = trigram(multiple_space)
    return [parsed_text, unigrams, bigrams, trigrams]


if __name__ == "__main__":
    # This is the Python main function.
    # You should be able to run
    # python cleantext.py <filename>
    # and this "main" function will open the file,
    # read it line by line, extract the proper value from the JSON,
    # pass to "sanitize" and print the result as a list.

    # YOUR CODE GOES BELOW.
    parser = argparse.ArgumentParser()
    parser.add_argument("filename", help="either .json or .bz2 file")
    args = parser.parse_args()
    fout = open(args.filename + "_output.txt", "w+")
    with open(args.filename) as fd:
        for line in fd:
            line = line.replace('false', 'False')
            line = line.replace('true', 'True')
            d = eval(line)
            fout.write(json.dumps(sanitize(d['body'])))
            fout.write("\n")
    fout.close()
