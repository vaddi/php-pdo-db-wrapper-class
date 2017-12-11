<?php

/*
	Simple Wrapper Class to connect a sqlite3 or mysql Database whith the same functions

TODO:
- LastInsterId not working on sqlite
*/

require_once( __DIR__ . '/class/Database.php' );

// Helper function
function boolify( $value ) {
	if( (int) $value === 1 ) {
		return "✔";
	} else {
		return "✘";
	}
}

// setup Database
//$type = 'MySQL';
$type = 'SQLite';
$table = 'users';

echo "<pre>";
echo " ################################## <br />";
echo " # PHP PDO Wrapper Class Examples # <br />";
echo " ################################## <br /><br />";

echo "Databasetype: $type <br />";
$Database = new Database( $type );

print_r( "Connected: " . boolify( $Database->connected ) . "<br />" );
print_r( 'Connection: ' . $Database->connection() . "<br />" );
echo "</pre>";

// Drop
echo "<pre>";
echo "Drop: ";
$query = "DROP TABLE IF EXISTS $table";
$Database->query( $query );
$result = $Database->execute();
print_r( boolify( $result ) );
echo "<br />";
echo "</pre>";

// Create
echo "<pre>";
$result=0;
echo "Create: ";
if( $type === 'SQLite' ) {
	$query = "CREATE TABLE $table ( 'id' INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, 'name' TEXT NULL )";
} else if( $type === 'MySQL' ){
	$query = "CREATE TABLE `$table` ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `name` TEXT NULL )";
}
$Database->query( $query );
$result = $Database->execute();
print_r( boolify( $result ) );
echo "<br />";
echo "</pre>";

// Insert Single
echo "<pre>";
$result=0;
echo "Insert Single: ";
$query = "INSERT INTO $table ( name ) VALUES ( 'Maik' )";
$Database->query( $query );
$result = (int) $Database->execute();
print_r( boolify( $result ) );
echo "<br />";
echo "LastInsertId: ";
print_r( $Database->lastInsertId( $table ) );
echo "<br />";
echo "</pre>";

// Insert Multiple
echo "<pre>";
$result=0;
echo "Insert Multiple: ";
$query = "INSERT INTO $table ( name ) VALUES ( :name )";
$Database->query( $query );
$Database->bind( ':name', "Hans" );
$Database->execute();
$Database->bind( ':name', "Klaus" );
$Database->execute();
$Database->bind( ':name', "Gabi" );
$result = (int) $Database->execute();
print_r( boolify( $result ) );
echo "<br />";
print_r( 'LastInsertId: ' . $Database->lastInsertId( $table ) . "<br />" );
echo "</pre>";

// simple select
echo "<pre>";
$result=0;
echo "Select: ";
$query = "SELECT * FROM $table";
$Database->query( $query );
$result = (int) $Database->execute();
print_r( boolify( $result ) );
print_r( '<br />--- <br />Query Result: <br />' );
print_r( $Database->resultObj() );
print_r( 'RowCount: ' . $Database->rowCount( $table ) . "<br />" );
echo "</pre>";

// Delete
echo "<pre>";
$result=0;
echo "Delete: ";
$query = "DELETE FROM $table WHERE name = ? OR name = ? OR name = ?";
$Database->query( $query );
$Database->bind( 1, "Hans" );
$Database->bind( 2, "Klaus" );
$Database->bind( 3, "Gabi" );
$result = (int) $Database->execute();
print_r( boolify( $result ) );
echo "<br />";
print_r( 'RowCount: ' . $Database->rowCount( $table ) . "<br />" );
echo "</pre>";

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
echo "<br />User id $userid updated";
echo "</pre>";

// simple select
echo "<pre>";
$result=0;
echo "Select: ";
$query = "SELECT * FROM $table";
$Database->query( $query );
$result = (int) $Database->execute();
print_r( boolify( $result ) );
print_r( '<br />--- <br />Query Result: <br />' );
print_r( $Database->resultObj() );
print_r( 'RowCount: ' . $Database->rowCount( $table ) . "<br />" );
echo "</pre>";

//echo "<pre>";
//print_r( 'LastInsertId: ' . $Database->lastInsertId( $table ) . "<br />" );
//print_r( "Connected: " . boolify( $Database->connected ) . "<br />" );
//print_r( 'Connection: ' . $Database->connection() . "<br />" );
//echo "</pre>";

// Insert Single
echo "<pre>";
$result=0;
echo "Insert Single: ";
$query = "INSERT INTO $table ( name ) VALUES ( 'Tim' )";
$Database->query( $query );
$result = (int) $Database->execute();
print_r( boolify( $result ) );
echo "<br />";
print_r( 'LastInsertId: ' . $Database->lastInsertId( $table ) . "<br />" );
echo "</pre>";

// simple select
echo "<pre>";
$result=0;
echo "Select: ";
$query = "SELECT * FROM $table";
$Database->query( $query );
$result = (int) $Database->execute();
print_r( boolify( $result ) );
print_r( '<br />--- <br />Query Result: <br />' );
print_r( $Database->resultObj() );
print_r( 'RowCount: ' . $Database->rowCount( $table ) . "<br />" );
echo "</pre>";

echo "<pre>";
$Database->close();
echo "Connected: " . boolify( $Database->connected ) . "<br />";
print_r( 'Connection: ' . (int) $Database->connection() . "<br />" );
echo "</pre>";

?>

