
printf "Starting Elasticsearch...\n"
$1
sleep 15

printf "Warming the JVM...\n"
for i in {1..5}
do
    ./bin/load-data.sh > /dev/null 2>&1
    ./console run elasticsearch --hide-errors=1 > /dev/null 2>&1
    printf "."

    ./bin/load-data.sh > /dev/null 2>&1
    ./console run sherlock --hide-errors=1 > /dev/null 2>&1
    printf "."

    ./bin/load-data.sh > /dev/null 2>&1
    ./console run elastica --hide-errors=1 > /dev/null 2>&1
    printf "."

    ./bin/load-data.sh > /dev/null 2>&1
    ./console run nervetattoo --hide-errors=1 > /dev/null 2>&1
    printf "."
done

print "\nBegin testing...\n"

./bin/load-data.sh > /dev/null 2>&1
sleep 5
./console run elasticsearch --hide-errors=1

./bin/load-data.sh > /dev/null 2>&1
sleep 5
./console run sherlock --hide-errors=1

./bin/load-data.sh > /dev/null 2>&1
sleep 5
./console run elastica --hide-errors=1

./bin/load-data.sh > /dev/null 2>&1
sleep 5
./console run nervetattoo --hide-errors=1
