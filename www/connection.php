<?php

	require 'vendor/autoload.php';
	use Laudis\Neo4j\Authentication\Authenticate;
	use Laudis\Neo4j\ClientBuilder;

	$client = ClientBuilder::create()
	    ->withDriver('bolt', 'bolt://neo4j:toor@localhost:7687?database=neo4j') // creates a bolt driver
	    ->withDriver('http', 'http://localhost:7474', Authenticate::basic('neo4j', 'toor')) // creates an http driver	    
	    ->withDefaultDriver('bolt')
	    ->build();
?>
