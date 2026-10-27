<?php
/*
function addAuthor($authorname, $authorBirthYear, $pdo){ /* stmt = statement 
	$stmt = $pdo->prepare("INSERT INTO forfattare (namn, fodelsear) VALUES (:namn, :fodelsear)"); /* :något är placeholders 
	$stmt->bindParam(':namn', $authorName, PDO::PARAM_STR);
	$stmt->bindParam(":fodelsear", $authorBirthYear, PDO::PARAM_INT);	
	return $stmt->execute(); returnerar true eller false och för att trigga if else satsen i index.php
}*/

function addAuthor($authorname, $authorBirthYear, $pdo) { 
	try {
		$stmt = $pdo->prepare("INSERT INTO forfattare (namn, fodelsear) VALUES (:namn, :fodelsear)");
		$stmt->bindParam(':namn', $authorname, PDO::PARAM_STR);
		$stmt->bindParam(':fodelsear', $authorBirthYear, PDO::PARAM_INT);	
		return $stmt->execute();
	} catch (PDOExeption $e) {
		return false;
	}
}

function addBook($boktitel, $bokutgivningsar, $bokforfattare, $pdo) { 
	try {
		$stmt = $pdo->prepare("INSERT INTO bok (titel, utgivningsar, forfattar_id) VALUES (:titel, :bokutgivningsar, :forfattar_id)");
		$stmt->bindParam(':titel', $boktitel, PDO::PARAM_STR);
		$stmt->bindParam(':bokutgivningsar', $bokutgivningsar, PDO::PARAM_INT);
		$stmt->bindParam(':forfattar_id', $bokforfattare, PDO::PARAM_INT);
		return $stmt->execute();
	} catch (PDOExeption $e) {
		return false;
	}
}
/*
function nyLantagare($lantagare) {

}*/

function lanaBok($lantagare, $bok, $todaysDate, $pdo) { 
	try {
		$stmt = $pdo->prepare("INSERT INTO lan (lantagar_id, bok_id, lanedatum) VALUES (:lantagar_id, :bok_id, :lanedatum)");
		return $stmt->execute([$lantagare, $bok, $todaysDate]);
	} catch (PDOExeption $e) {
		return false;
	}
}

function renderBookLoans($loanStatus, $pdo) {
	$queryString = "SELECT lan.lanedatum, lan.aterlamningsdatum, bok.titel, lantagare.namn FROM lan 
		INNER JOIN bok ON lan.bok_id = bok.bok_id 
		INNER JOIN lantagare ON lan.lantagar_id = lantagare.lantagar_id";

		if($loanStatus === 0) {
			$queryString .= " WHERE aterlamningsdatum IS NULL ";
		} else {
			$queryString .= " WHERE aterlamningsdatum IS NOT NULL ";
		}

		$queryString .= "ORDER BY lan.lanedatum ASC";

	try {	
		$result = $pdo->query($queryString)->fetchAll();
		return $result;
	} catch (PDOExeption $e) {
		return false;
	}
}

?>