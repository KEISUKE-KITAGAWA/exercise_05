<?php 

function connectDb()
{
  try {
    return new PDO(DSN, USER, PASSWORD);
  } catch (PDOException $e) {
    echo $e->getMessage();
    exit;
  }
}

function h($s)
{
  // ENT_QUOTES	シングルクオートとダブルクオートを共に変換する。
  return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}
?>