ElasticSearch PHP clients benchmark
===================================

We run all the PHP client for ElasticSearch (if we miss one, send a PR!) and ask them to perform a lot of requests
and we compute some statistics about it.

More than the speed or memory, this benchmark is also about usage, API and examples for each client.

They are configured the same way:
- no logs
- two nodes cluster (as we run a test with the master node down, to test fallback)
- keep alive on the connection (if possible)

About versions
--------------

We use Sherlock `0.2.0.*@dev` as the 0.1 branch is deprecated. It's still alpha.

To compare
==========

- Keep Alive connexion?
- Memory consumption
- Speed
- Connexion handling, failover
- Query builder? Json only?
- Documentation, community, etc...
- Does it handle:
    - routing?
    - suggestion API?
    - mtl queries?
