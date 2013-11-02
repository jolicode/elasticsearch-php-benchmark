ElasticSearch PHP clients benchmark
===================================

We run all the PHP client for ElasticSearch (if we miss one, please tell us!).

They are configured the same way:
- no logs
- two nodes cluster (as we run a test with the master node down)
- keep alive on the connection (if possible)

About the results
-----------------

We mainly test memory consumption and ease of use here,
speed is also a criteria but is suggest to network variation, ElasticSearch state... Be careful about the results.

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
