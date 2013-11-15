./bin/load-data.sh

curl -XPOST 'http://localhost:9200/client_bench/_refresh'
sleep 5
./console run elasticsearch --hide-errors=1
curl -XPOST 'http://localhost:9200/client_bench/_refresh'
sleep 5
./console run sherlock --hide-errors=1
curl -XPOST 'http://localhost:9200/client_bench/_refresh'
sleep 5
./console run elastica --hide-errors=1
curl -XPOST 'http://localhost:9200/client_bench/_refresh'
sleep 5
./console run nervetattoo --hide-errors=1
