curl -XDELETE 'http://localhost:9200/client_bench'

curl -XPUT 'http://localhost:9200/client_bench' -d '
{
  "number_of_shards": 1,
  "number_of_replicas": 1
}'


curl -XPUT 'http://localhost:9200/client_bench/post/_mapping' -d '
{
  "post": {
    "properties": {
      "content": { "type": "string" },
      "author": {
        "type": "object",
        "properties": {
          "name": {
            "type": "string",
            "index": "not_analyzed"
          }
        }
      }
    }
  }
}'


curl -XPOST "http://localhost:9200/client_bench/post/1" -d '
{
    "post_id": 1,
    "topic_id": 2,
    "post_date": "2013/10/30 13:37:00",
    "content": "ElasticSearch is also logs logs logs usefull for logs storage and logs analysis.",
    "author": {
        "name": "Damien",
        "author_id": 1
    }
}'


curl -XPOST "http://localhost:9200/client_bench/post/2" -d '
{
    "post_id": 2,
    "topic_id": 2,
    "post_date": "2013/10/30 13:47:00",
    "content": "Ah non attention, là on voit qu''on a beaucoup à travailler sur nos logs car c''est un très, très gros travail et ça, c''est très dur, et, et, et...",
    "author": {
        "name": "Shay Banon",
        "author_id": 2
    }
}'


curl -XPOST "http://localhost:9200/client_bench/post/3" -d '
{
    "post_id": 3,
    "topic_id": 2,
    "post_date": "2013/10/30 14:07:00",
    "content": "Tu comprends, après il faut s''intégrer tout ça dans les logs",
    "author": {
        "name": "Shay Banon",
        "author_id": 2
    }
}'


curl -XPOST "http://localhost:9200/client_bench/post/4" -d '
{
    "post_id": 4,
    "topic_id": 2,
    "post_date": "2013/11/01 13:37:00",
    "content": "Je ne voudrais pas rentrer les logs dans des choses trop dimensionnelles",
    "author": {
        "name": "Ternel",
        "author_id": 3
    }
}'


curl -XPOST "http://localhost:9200/client_bench/post/5" -d '
{
    "post_id": 5,
    "topic_id": 2,
    "post_date": "2013/11/02 13:37:00",
    "content": "Donc on n''est jamais seul spirituellement ! Même avec nos logs.",
    "author": {
        "name": "Ternel",
        "author_id": 3
    }
}'


curl -XPOST 'http://localhost:9200/client_bench/_search?pretty=true' -d '
{ "query": { "match_all" : {} } }'


curl -XPOST 'http://localhost:9200/client_bench/_search?pretty=true' -d '
{
  "query": {
    "match_all" : {}
  },
  "facets" : {
    "names" : { "terms" : {"field" : "author.name"} }
  }
}'

