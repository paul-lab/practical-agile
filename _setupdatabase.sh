#!/bin/bash

# After running this, modify the settings in:
#  include/dbconfig.inc.php

pa_user=practicalagile
pa_pass=practicalagile_password
database=practicalagile
create_pa_user=0
#create_pa_user=1

# Credentials for MySQL administrator.
admin_user=root
admin_pass=root_password

mysql=mysql
mysqlport=3311
# Or 3306?

function run()
{
  echo "$@" >&2
  "$@"

  if [ $? != 0 ]
  then
    echo 'Bailing out!'
    exit 1
  fi
}

echo "Creating database $database based on _dbstructure.txt" >&2
echo "^C to abort" >&2

rm -f database.txt
if [ "$create_pa_user" = 1 ]
then
  run echo "CREATE USER '$pa_user' IDENTIFIED BY '$pa_pass';" >> database.txt
  run echo "CREATE USER '$pa_user'@'localhost' IDENTIFIED BY '$pa_pass';" >> database.txt
fi
run echo "CREATE database $database;" >> database.txt
run echo "GRANT ALL PRIVILEGES ON $database.* TO $pa_user@localhost;" >> database.txt

run $mysql --user=$admin_user --password=$admin_pass --port=$mysqlport < database.txt
rm -f database.txt

run $mysql --user=$admin_user --password=$admin_pass --port=$mysqlport $database < _dbstructure.txt

echo Importing data >&2
run $mysql  --user=$admin_user --password=$admin_pass --port=$mysqlport $database < _data.sql

echo Importing Reports >&2
run $mysql  --user=$admin_user --password=$admin_pass --port=$mysqlport $database < _queries.sql

