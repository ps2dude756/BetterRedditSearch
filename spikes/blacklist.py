import requests
import json


# subreddits

r = requests.get(r'http://www.reddit.com/reddits.json?limit=100')

data = json.loads(r.text)
after = None

for child in data['data']['children']:
    print child['data']['url']
    after =  child['data']['name']

print "\r\n"

r = requests.get(r'http://www.reddit.com/reddits.json?limit=100&after=' + after)

data = json.loads(r.text)
after = None

for child in data['data']['children']:
    print child['data']['url']
    after =  child['data']['id']

