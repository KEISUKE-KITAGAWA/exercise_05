<?php

use PhpParser\Node\Stmt;

require_once('config.php');
require_once('functions.php');

$dbh = connectDb();
$sql = "SELECT * FROM plans WHERE status ='notyet'ORDER BY due_date ASC";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$notyet_tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql3 = "SELECT * FROM plans WHERE status ='done' ";
$stmt3 = $dbh->prepare($sql3);
$stmt3->execute();
$done_tasks = $stmt3->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $title = $_POST['title'];
  $date = $_POST['date'];

  $errors = [];

  if ($title == "") {
    $errors[] = '学習内容を入力してください';
  }
  if ($date == "") {
    $errors[] = '期限日を入力してください';
  }

  if (empty($errors)) {
    $sql2 = 'INSERT INTO plans (title, due_date) VALUES (:title, :due_date)';
    $stmt2 = $dbh->prepare($sql2);
    $stmt2->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt2->bindValue(":due_date", date("Y-m-d", strtotime($date)), PDO::PARAM_STR);
    $stmt2->execute();

    header('Location: index.php');
    exit;
  }
}


?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>タスク一覧</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <h1>学習管理アプリ</h1>
  <form action="" method="post">
    <div>
      <label for="">学習内容:</label>
      <input type="text" name="title">
    </div>
    <div>
      <label for="">期限日:</label>
      <input type="date" name="date">
      <input type="submit" value="追加">
    </div>
  </form>
  <ul>
    <?php if (!$errors == "") : ?>
      <?php foreach ($errors as $error) : ?>
        <li><?= $error ?></li>
      <?php endforeach; ?>
    <?php endif; ?>
  </ul>

  <h2>未達成</h2>
  <ul>
    <?php foreach ($notyet_tasks as $notyet_task) : ?>
      <?php if(date('Y-m-d') >= $notyet_task['due_date']) : ?>
        <li class = "expired">
          <a href="done.php?id=<?= h($notyet_task['id']) ?>">[完了]</a>
          <a href="edit.php?id=<?= h($notyet_task['id']) ?>">[編集]</a>
          <?= $notyet_task['title'] ?>
        </li>
      <?php else : ?>
        <li>
          <a href="done.php?id=<?= h($notyet_task['id']) ?>">[完了]</a>
          <a href="edit.php?id=<?= h($notyet_task['id']) ?>">[編集]</a>
          <?= $notyet_task['title'] ?>
        </li>
      <?php endif ; ?>
    <?php endforeach; ?>
  </ul>
  <hr>
  <h2>達成</h2>
  <ul>
    <?php foreach($done_tasks as $done_task) : ?>
      <li><?= h($done_task['title']) ?></li>
    <?php endforeach ; ?>
  </ul>
</body>

</html>