<?php
require_once('functions.php');
require_once('config.php');

$id = $_GET['id'];

$dbh = connectDB();
$sql = "SELECT * FROM plans WHERE id = :id";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$plan = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $title = $_POST['title'];
  $due_date = $_POST['due_date'];
  $errors = [];
  
  if ($title == "") {
    $errors['title'] = "タイトルが入力されていません";
  }
  if ($due_date == "") {
    $errors['due_date'] = "期限日が入力されていません";
  }
  if ($title == $plan['title'] and $due_date == $plan['due_date']){
    $errors['both'] = "変更内容がありません";
  }

  if (empty($errors)) {
    $dbh = connectDB();
    $sql = "UPDATE plans SET title = :title, due_date = :due_date WHERE id = :id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindValue(":due_date", date("Y-m-d", strtotime($due_date)), PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    header('Location:index.php');
    exit;
  }
}



?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>タスクの編集</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <h1>タスクの編集</h1>
  <form action="" method="post">
    <div>
      <label for="">学習内容</label><input type="text" name="title" value="<?= h($plan['title']) ?>">
    </div>
    <div>
      <label for="">期限日</label><input type="date" name="due_date" value="<?= h($plan['due_date']) ?>">
    </div>
    <input type="submit" value="変更"><button><a href="index.php">戻る</a></button>
  </form>
  <?php if(!$errors == "") : ?>
    <ul class = "error-list">
      <?php foreach($errors as $error) : ?>
        <li><?= h($error) ?></li> 
      <?php endforeach ;?>
    <?php endif ; ?>
  </ul>
</body>

</html>