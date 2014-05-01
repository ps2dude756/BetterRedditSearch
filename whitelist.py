import requests
import json


# search
# GET [/r/subreddit]/search[ .json | .xml ]

r = requests.get(r'http://www.reddit.com/r/funny/search.json?q=cat')

data = json.loads(r.text)

#print data['data']['children'][0]['data']['id']

for child in data['data']['children']:
    print child['data']['id'], "\r\n", child['data']['author'], "\r\n", child['data']['title']
    print


