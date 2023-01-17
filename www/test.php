<?php
//echo $_SERVER['DOCUMENT_ROOT'];
file_put_contents('/home/demo/public_html/wp-logs/cron.log', 'Server Cron Test'.chr(13).chr(10), FILE_APPEND);
