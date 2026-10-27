<?php
/*require för att kräva att de finns varje gång sidan laddas (men inte i samma fil) och once för att koden bara behöver existera en gång per projekt*/
require_once "config.php";
require_once "functions.php";

$allAuthors = $pdo->query("SELECT * FROM forfattare")->fetchAll();
$allBooks = $pdo->query("SELECT * FROM bok")->fetchAll();
$allLantagare = $pdo->query("SELECT * FROM lantagare")->fetchAll();
$allAvailableBooks = renderBookLoans(1, $pdo);
$allLoanedBooks = renderBookLoans(0, $pdo);

/*För att se rå data
$books = $pdo->query("SELECT * FROM bok")->fetchAll();
print_r($books) Som console log */

$booksWithAuthorName = $pdo->query("SELECT * FROM bok INNER JOIN forfattare ON bok.forfattar_id = forfattare.forfattar_id ORDER BY bok.utgivningsar DESC")->fetchAll();
//print_r($booksWithAuthorName);

/*
foreach($booksWithAuthorName as $singleBook) {
  echo "Titel: "$singleBook["titel"] . " Författare: "$singleBook["namn"] . " Utgivningsår: "$singleBook["utgivningsar"]";
  }*/

  /* Kolla om knappen har tryckts och spara datan */
  if(isset($_POST["forfattare_skicka"])){
    $authorname = $_POST["namn"];
    $authorBirthYear = $_POST["fodelsear"];

    /* Lägga till i databasen */
    if(addAuthor($authorname, $authorBirthYear, $pdo)) {
      $feedbackmessage = [1, "Författare skapad!"];
    }
    else {
      $feedbackmessage = [0, "Ett fel har inträffat"];
    }
  }

  if(isset($_POST["bok_skicka"])){
      $boktitel = $_POST["titel"];
      $bokutgivningsar = $_POST["utgivningsar"];
      $bokforfattare = $_POST["forfattar_id"];

      /* Lägga till i databasen */
      if(addBook($boktitel, $bokutgivningsar, $bokforfattare, $pdo)) {
        $feedbackmessage = [1, "Bok skapad!"];
      }
      else {
        $feedbackmessage = [0, "Ett fel har inträffat"];
      }
    }

  if(isset($_POST["lana_skicka"])){
    $todaysDate = date("Y-m-d");
    $lantagareId = $_POST["lantagar_id"];
      $bokId = $_POST["bok_id"];

      /* Lägga till i databasen */
      if(lanaBok($lantagareId, $bokId, $todaysDate, $pdo)) {
        $feedbackmessage = [1, "Bok lånad!"];
      }
      else {
        $feedbackmessage = [0, "Ett fel har inträffat"];
      }
    }


?>

<!doctype html>
<html lang="sv">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Bibliotek</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <h1 class="mb-4">Bibliotek</h1>

    <!-- Feedback message after adding author -->
    <?php if (isset($feedbackmessage)): ?>
      <div class="alert <?= $feedbackmessage[0] ? 'alert-success' : 'alert-danger' ?>">
        <?= htmlspecialchars($feedbackmessage[1]) ?>
      </div>
    <?php endif; ?>

    <div class="row">
    <?php foreach ($booksWithAuthorName as $book): ?>
      <div class="col-md-4 mb-3">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h2 class="card-title"><?= htmlspecialchars($book["titel"]) ?></h2>
            <h3 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($book["namn"]) ?></h3>
            <p class="card-text">Utgiven <?= htmlspecialchars($book["utgivningsar"]) ?></p>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  </div>

  <div class="row g-4 mb-5">

      <!-- Ny författare -->
      <div class="col-lg-4">
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <h5 class="card-title">Ny författare</h5>
            <form method="post" novalidate>
              <div class="mb-3">
                <label for="namn" class="form-label">Namn</label>
                <input type="text" class="form-control" id="namn" name="namn" required>
              </div>
              <div class="mb-3">
                <label for="fodelsear" class="form-label">Födelseår</label>
                <input type="number" class="form-control" id="fodelsear" name="fodelsear"
                       min="1" max="<?= date('Y') ?>" required>
              </div>
              <button type="submit" name="forfattare_skicka" value="1" class="btn btn-primary w-100">
                Lägg till författare
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Ny bok -->
      <div class="col-lg-4">
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <h5 class="card-title">Ny bok</h5>
            <form method="post" novalidate>
              <div class="mb-3">
                <label for="titel" class="form-label">Titel</label>
                <input type="text" class="form-control" id="titel" name="titel" required>
              </div>
              <div class="mb-3">
                <label for="utgivningsar" class="form-label">Utgivningsår</label>
                <input type="number" class="form-control" id="utgivningsar" name="utgivningsar"
                       min="1" max="<?= date('Y') ?>" required>
              </div>
              <div class="mb-3">
                <label for="forfattar_id" class="form-label">Författare</label>
                <select class="form-select" id="forfattar_id" name="forfattar_id" required>
                  <option value="">Välj författare…</option>
                  <?php foreach ($allAuthors as $author): ?>
                    <option value="<?= /*shorthand för echo*/ (int)$author["forfattar_id"] ?>">
                      <?= htmlspecialchars($author["namn"]) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <button type="submit" name="bok_skicka" value="1" class="btn btn-primary w-100">
                Lägg till bok
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Ny låntagare -->
      <div class="col-lg-4">
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <h5 class="card-title">Ny låntagare</h5>
            <form method="post" novalidate>
              <div class="mb-3">
                <label for="lan_tagare_namn" class="form-label">Namn</label>
                <input type="text" class="form-control" id="lan_tagare_namn" name="lan_tagare_namn" required>
              </div>
              <div class="mb-3">
                <label for="epost" class="form-label">E-post</label>
                <input type="email" class="form-control" id="epost" name="epost" required>
              </div>
              <button type="submit" name="lantagare_skicka" value="1" class="btn btn-primary w-100">
                Lägg till låntagare
              </button>
            </form>
          </div>
        </div>
      </div>

    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <h5 class="card-title">Låna en bok</h5>
            <form method="post" novalidate>
              <div class="mb-3">
                <label for="lantagar_id" class="form-label">Låntagare</label>
                <select class="form-select" id="lantagar_id" name="lantagar_id" required>
                  <option value="">Välj låntagare</option>
                  <?php foreach ($allLantagare as $row): ?>
                    <option value="<?= (int)$row["lantagar_id"] ?>">
                      <?= htmlspecialchars($row["namn"]) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="bok_id" class="form-label">Bok</label>
                <select class="form-select" id="bok_id" name="bok_id" required>
                  <option value="">Välj bok</option>
                  <?php foreach ($allBooks as $row): ?>
                    <option value="<?= (int)$row["bok_id"] ?>">
                      <?= htmlspecialchars($row["titel"]) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <button type="submit" name="lana_skicka" value="1" class="btn btn-primary w-100">
                Låna bok
              </button>
            </form>
          </div>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered align-middle">
          <thead class="table-dark">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Lånedatum</th>
              <th scope="col">Återlämningsdatum</th>
              <th scope="col">Titel</th>
              <th scope="col">Låntagare</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($allAvailableBooks as $i => $rad): ?>
              <tr>
                <th scope="row"><?= $i + 1 ?></th>
                <td><?= htmlspecialchars($rad["lanedatum"]) ?></td>
                <td>
                  <?php if (!empty($rad["aterlamningsdatum"])): ?>
                    <?= htmlspecialchars($rad["aterlamningsdatum"]) ?>
                  <?php else: ?>
                    <span class="badge bg-warning text-dark">Ej återlämnad</span>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($rad["titel"]) ?></td>
                <td><?= htmlspecialchars($rad["namn"]) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
    </div>

      <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered align-middle">
          <thead class="table-dark">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Lånedatum</th>
              <th scope="col">Återlämningsdatum</th>
              <th scope="col">Titel</th>
              <th scope="col">Låntagare</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($allLoanedBooks as $i => $rad): ?>
              <tr>
                <th scope="row"><?= $i + 1 ?></th>
                <td><?= htmlspecialchars($rad["lanedatum"]) ?></td>
                <td>
                  <?php if (!empty($rad["aterlamningsdatum"])): ?>
                    <?= htmlspecialchars($rad["aterlamningsdatum"]) ?>
                  <?php else: ?>
                    <span class="badge bg-warning text-dark">Ej återlämnad</span>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($rad["titel"]) ?></td>
                <td><?= htmlspecialchars($rad["namn"]) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
    </div>

  <!-- Bootstrap 5 JS bundle (optional, needed for interactive components) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</html>