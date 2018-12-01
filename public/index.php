<?php
$cluster = Cassandra::cluster()->withContactPoints('cassandra')->withPort(9042)->withIOThreads(5)->build();
$session = $cluster->connect(); // create session and connect to cassandra server
$cqlKeyspace = "CREATE KEYSPACE IF NOT EXISTS project1 WITH replication = {'class': 'SimpleStrategy', 'replication_factor': '1'}  AND durable_writes = true;";
$session->execute($cqlKeyspace);
$session->execute('USE project1'); // switch to project1 keyspace
//$session->execute('DROP TABLE IF EXISTS project1.test'); // debug purpose
$session->execute('CREATE TABLE IF NOT EXISTS project1.test ( time1 timestamp, PRIMARY KEY (time1) );'); // create a table `test` (if it does not exists) with the field `time1`
$current_time = date('Y-m-d H:i:s');
$session->execute("INSERT INTO project1.test( time1) values ( '$current_time' )");
$statement = new Cassandra\SimpleStatement("SELECT * FROM test");
$result = $session->execute($statement);
echo "Result contains " . $result->count() . " rows";
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Project1</title>
    <style>
        table, th, td {
            border: 1px solid black;
        }
    </style>
</head>
<body>
<table>
    <tr>
        <th>Date &amp; Time</th>
    </tr>
    <?php
    foreach ($result as $row) {
        echo '<tr><td>' . $row['time1']->toDateTime()->format('Y-m-d H:i:s') . '</td></tr>';
    }
    ?>
</table>
</body>
</html>