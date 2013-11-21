./bin/load-data.sh
sleep 5
./console run elasticsearch --hide-errors=1

./bin/load-data.sh
sleep 5
./console run sherlock --hide-errors=1

./bin/load-data.sh
sleep 5
./console run elastica --hide-errors=1

./bin/load-data.sh
sleep 5
./console run nervetattoo --hide-errors=1
