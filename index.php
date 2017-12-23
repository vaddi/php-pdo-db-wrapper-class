<?php
/*
	Simple Wrapper Class to connect to a Database whith the same function calls

	current supported Databases:
	MySQL
	SQLite
*/

// setup Database
//$type = 'MySQL';
$type = 'SQLite';
$table = 'users';

// check for missing php libraries
if( ! is_file( __DIR__ . '/config.php' ) ) {
  echo "Please copy and edit <strostrongg>config.php</strong> to setup Database connections<br />";
  echo "cp config.php.example config.php && vim config.php";
  exit;
}
$libs = array( 'pdo_mysql' => 'php-mysql', 'pdo_sqlite' => 'php-sqlite3' );
foreach( $libs as $key => $lib ) {
  if( ! extension_loaded( $key ) ) {
    echo "Failed to load " . $key . "</br>";
    echo "Please install missing (Example on an Ubuntu 16.04): <br />";
    echo "sudo apt install " . $lib . "<br />";
    exit;
  }
}

// load the database class
require_once( __DIR__ . '/class/Database.php' );

echo "<pre>";
echo " ################################## <br />";
echo " # PHP PDO Wrapper Class Examples # <br />";
echo " ################################## <br />";
echo "</pre>";

// Helper function print bool
function boolify( $value ) {
	return (int) $value === 1 ? "✔" : "✘";
}

// helper function show result as html table
function tablify( $data ) {
	echo "<table border='1'>";
	echo "<thead>";
	echo "<tr>";
	foreach( $data as $row ) {
		$total = is_object( $row) ? count( get_object_vars( $row ) ) : count($row);
		foreach ( $row as $key => $entry ) {
			if( $count++ >= $total ) break;
			echo "<th>" . $key . "</th>";
		}
	}
	echo "</tr>";
	echo "</thead>";
	echo "<tbody>";
	foreach( $data as $row ) {
		echo "<tr>";
		foreach ( $row as $key => $entry ) {
			echo "<td>";
			echo $entry;
			echo "</td>";
		}
		echo "</tr>";
	}
	echo "<tbody>";
	echo "</table>";
}

echo "<pre>";
echo "Databasetype: $type <br />";
$Database = new Database( $type );

print_r( "Connected: " . boolify( $Database->connected ) . "<br />" );
print_r( 'Connection: ' . $Database->connection() . "<br />" );
echo "</pre>";

// check SQLite Database file
if( $type === 'SQLite' && SQLITE_TYPE === 'FILE' && ! is_writeable( SQLITE_FILE ) ) {
	echo "SQLite file not writeable by webserver user, please add write permissions to file and Folder! <br />";
	echo "sudo chown -R www-data " .  dirname( SQLITE_FILE ). "<br />";
	exit;
}

// check Database connection
if( ! $Database->connection() ) {
	if ( SQLITE_TYPE === 'MEMORY' ) {
		echo "SQLite type: <strong>" . SQLITE_TYPE . "</strong> is not full implemented. <br />See <a href='https://www.sqlite.org/inmemorydb.html'>https://www.sqlite.org/inmemorydb.html</a> for example. <br /><br />";
	}
	echo "No Database connection available, abort script. <br />";
	exit;
}

echo "<hr />";

// Drop
echo "<pre>";
echo "Drop: ";
$query = "DROP TABLE IF EXISTS $table";
$Database->query( $query );
$result = $Database->execute();
print_r( boolify( $result ) );
if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong><br />" );
echo "</pre><hr />";

// Create
echo "<pre>";
$result=0;
echo "Create: ";
if( $type === 'SQLite' ) {
	$query = "CREATE TABLE $table ( 'id' INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, 'name' TEXT NULL, 'email' TEXT NULL )";
} else if( $type === 'MySQL' ){
	$query = "CREATE TABLE `$table` ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `name` TEXT NULL, `email` TEXT NULL )";
}
$Database->query( $query );
$result = $Database->execute();
print_r( boolify( $result ) );
if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong><br />" );
echo "</pre><hr />";

// Insert Single
echo "<pre>";
$result=0;
echo "Insert Single: ";
$query = "INSERT INTO $table ( name ) VALUES ( 'Maik' )";
$Database->query( $query );
$result = (int) $Database->execute();
print_r( boolify( $result ) );
if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong>" );
echo "<br />";
echo "LastInsertId: ";
print_r( $Database->lastInsertId( $table ) );
echo "<br />";
echo "</pre><hr />";

// Insert Multiple
echo "<pre>";
$result=0;
echo "Insert 3 Entries: ";
$query = "INSERT INTO $table ( name ) VALUES ( :name )";
$Database->query( $query );
$Database->bind( ':name', "Hans" );
$Database->execute();
$Database->bind( ':name', "Klaus" );
$Database->execute();
$Database->bind( ':name', "Gabi" );
$result = (int) $Database->execute();
print_r( boolify( $result ) );
if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong>" );
echo "<br />";
print_r( 'LastInsertId: ' . $Database->lastInsertId( $table ) . "<br />" );
echo "</pre><hr />";

// simple select
echo "<pre>";
$result=0;
echo "Select: ";
$query = "SELECT * FROM $table";
$Database->query( $query );
$result = (int) $Database->execute();
print_r( boolify( $result ) );
if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong>" );
print_r( '<br />--- <br />Query Result: <br />' );
echo tablify( $Database->resultObj() );
//print_r( $Database->resultset() );
print_r( 'RowCount: ' . $Database->rowCount( $table ) . "<br />" );
echo "</pre><hr />";

// Delete
echo "<pre>";
$result=0;
echo "Delete 3 Entries: ";
$query = "DELETE FROM $table WHERE name = ? OR name = ? OR name = ?";
$Database->query( $query );
$Database->bind( 1, "Hans" );
$Database->bind( 2, "Klaus" );
$Database->bind( 3, "Gabi" );
$result = (int) $Database->execute();
print_r( boolify( $result ) );
if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong>" );
echo "<br />";
print_r( 'RowCount: ' . $Database->rowCount( $table ) . "<br />" );
echo "</pre><hr />";

// simple select
echo "<pre>";
$result=0;
echo "Select: ";
$query = "SELECT * FROM $table";
$Database->query( $query );
$result = (int) $Database->execute();
print_r( boolify( $result ) );
if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong>" );
print_r( '<br />--- <br />Query Result: <br />' );
echo tablify( $Database->resultObj() );
//print_r( $Database->resultset() );
print_r( 'RowCount: ' . $Database->rowCount( $table ) . "<br />" );
echo "</pre><hr />";

// Update
echo "<pre>";
$userid = 1;
echo "Update: ";
$query = "UPDATE $table SET name = :name  WHERE id = :id";
$Database->query( $query );
$Database->bind( ':name', "Greta" );
$Database->bind( ':id', $userid );
$result = (int) $Database->execute();
print_r( boolify( $result ) );
if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong>" );
echo "<br />User id $userid updated";
echo "</pre><hr />";

// simple select
echo "<pre>";
$result=0;
echo "Select: ";
$query = "SELECT * FROM $table";
$Database->query( $query );
$result = (int) $Database->execute();
print_r( boolify( $result ) );
if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong>" );
print_r( '<br />--- <br />Query Result: <br />' );
echo tablify( $Database->resultObj() );
//print_r( $Database->resultset() );
print_r( 'RowCount: ' . $Database->rowCount( $table ) . "<br />" );
echo "</pre><hr />";

// Insert Single
echo "<pre>";
$result=0;
echo "Insert Single: ";
$query = "INSERT INTO $table ( name ) VALUES ( 'Tim' )";
$Database->query( $query );
$result = (int) $Database->execute();
print_r( boolify( $result ) );
if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong>" );
echo "<br />";
print_r( 'LastInsertId: ' . $Database->lastInsertId( $table ) . "<br />" );
echo "</pre><hr />";

// simple select
echo "<pre>";
$result=0;
echo "Select: ";
$query = "SELECT * FROM $table";
$Database->query( $query );
$result = (int) $Database->execute();
print_r( boolify( $result ) );
if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong>" );
print_r( '<br />--- <br />Query Result: <br />' );
echo tablify( $Database->resultObj() );
//print_r( $Database->resultset() );
print_r( 'RowCount: ' . $Database->rowCount( $table ) . "<br />" );
echo "</pre><hr />";

// TODO add examples for other class functions
// $Database->single();

// Transactions
echo "<pre>";
print_r( "Begin Transaction: " . boolify( $Database->beginTransaction() ) . "<br />" );
print_r( "Insert Single: " );
$query = "INSERT INTO $table ( name ) VALUES ( 'Rainer' )";
$Database->query( $query );
$result = (int) $Database->execute();
print_r( boolify( $result ) );
if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong>" );
echo "<br />";
print_r( 'LastInsertId: ' . $Database->lastInsertId( $table ) . "<br />" );
if( $result ) {
	print_r( "End Transaction: " . boolify( $Database->endTransaction() ) . "<br />" );
} else {
	print_r( "Cancel Transaction: " . boolify( $Database->cancelTransaction() ) . "<br />" );
}
echo "</pre><hr />";

// simple select
echo "<pre>";
$result=0;
echo "Select: ";
$query = "SELECT * FROM $table";
$Database->query( $query );
$result = (int) $Database->execute();
print_r( boolify( $result ) );
if( $result ) print_r( "<br />Query: <strong>" . $query . "</strong>" );
print_r( '<br />--- <br />Query Result: <br />' );
echo tablify( $Database->resultObj() );
//print_r( $Database->resultset() );
print_r( 'RowCount: ' . $Database->rowCount( $table ) . "<br />" );
echo "</pre><hr />";

// Debugging
echo "<pre>";
echo "Debug Params: ";
$Database->debugDumpParams();
echo "<br />";
print_r( "Query String: " . $Database->queryString() . "<br />" );
print_r( "Error Info: " );
print_r( $Database->errorInfo() );
print_r( "<br />" );
echo "</pre>";

echo "<pre>";
$Database->close();
echo "Connected: " . boolify( $Database->connected ) . "<br />";
print_r( 'Connection: ' . (int) $Database->connection() . "<br />" );
echo "</pre>";

?>

