<?php
$p = getenv('DB_PASS');
echo 'len=' . strlen($p) . '<br>';
echo 'first=' . substr($p, 0, 2) . '<br>';
echo 'last=' . substr($p, -2) . '<br>';
echo 'has_space_or_quote=' . (preg_match('/[\s"\']/', $p) ? 'YES' : 'NO') . '<br>';
echo 'host=' . getenv('DB_HOST') . '<br>';
echo 'port=' . getenv('DB_PORT') . '<br>';
echo 'user=' . getenv('DB_USER') . '<br>';
