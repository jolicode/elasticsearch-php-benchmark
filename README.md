ElasticSearch PHP clients benchmark
===================================

We run all the PHP clients for Elasticsearch (if we miss one, send a PR!) and ask them to perform a lot of requests,
we compute some statistics about it.

More than the speed or memory, this benchmark is also about usage, API and examples for each clients.

They are configured the same way:
- no logs
- two nodes cluster (as we run a test with the master node down, to test fallback)
- keep alive on the connection (if possible)

About versions
--------------

- **ruflin/elastica**: v0.90.5.0
- **sherlock/sherlock**: dev-master
- **elasticsearch/elasticsearch**: v0.4.2
- **nervetattoo/elasticsearch**: v2.3.0

Data extracted 2013/11/15
-------------------------

Runned with PHP 5.3 on Ubuntu 12.04.

<table>
<tr><th> </th><th>elasticsearch</th><th>sherlock</th><th>elastica</th><th>nervetattoo</th></tr>
 <tr><td>getDocument</td><td>9772</td><td>2235</td><td>1549</td><td>1109</td></tr>
 <tr><td>searchDocument</td><td>5753</td><td>2518</td><td>1859</td><td>1118</td></tr>
 <tr><td>searchDocumentWithFacet</td><td>5803</td><td>3109</td><td>2047</td><td>1057</td></tr>
 <tr><td>searchOnDisconnectNode</td><td>12419</td><td>0</td><td>2859</td><td>0</td></tr>
 <tr><td>searchSuggestion</td><td>7015</td><td>0</td><td>24582</td><td>2203</td></tr>
 <tr><td>indexRefresh</td><td>4916</td><td>0</td><td>1489</td><td>1502</td></tr>
 <tr><td>indexStats</td><td>5107</td><td>0</td><td>1674</td><td>1402</td></tr>
 <tr><td>Total time</td><td>55564</td><td>11004</td><td>37088</td><td>9844</td></tr>
 <tr><td>Memory</td><td>28311552</td><td>12320768</td><td>10747904</td><td>8388608</td></tr>
</table>

Run the bench
-------------

Boot Elasticsearch on the default port and run `./bin/run-all.sh`.

Read about the whole results
----------------------------

This benchmark is part of an article describing [Elasticsearch PHP clients](http://jolicode.com/).
