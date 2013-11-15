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

We use Sherlock `0.2.0.*@dev` as the 0.1 branch is deprecated. It's still alpha.

Read about the whole results
----------------------------

This benchmark is part of an article describing [Elasticsearch PHP clients](http://jolicode.com/).
