
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Install File</title>
</head>

<body>

    <head>
        <meta charset="utf-8">
        <title>DK CT</title>
        <link rel="stylesheet" href="../css/bootstrap.css" />
        <link rel="stylesheet" href="../css/app.css" />
        <link rel="stylesheet" href="../css/font-awesome.css" />
        <link rel="stylesheet" href="../css/tether.min.css" />
        <link rel="stylesheet" href="../css/animate.css">
        <link rel="stylesheet" href="../css/angular-ui-notification.min.css">

        <script type="text/javascript" src="../js/jquery.min.js"></script>
        <script type="text/javascript" src="../js/tether.min.js"></script>
        <script type="text/javascript" src="../js/bootstrap.min.js"></script>
        <script type="text/javascript" src="../js/angular.min.js"></script>

    </head>

    <body>
       <?php
            function sqlImport($file){

                    $delimiter = ';';
                    $file = fopen($file, 'r');
                    $isFirstRow = true;
                    $isMultiLineComment = false;
                    $sql = '';

                    while (!feof($file)) {

                        $row = fgets($file);

                        // remove BOM for utf-8 encoded file
                        if ($isFirstRow) {
                            $row = preg_replace('/^\x{EF}\x{BB}\x{BF}/', '', $row);
                            $isFirstRow = false;
                        }

                        // 1. ignore empty string and comment row
                        if (trim($row) == '' || preg_match('/^\s*(#|--\s)/sUi', $row)) {
                            continue;
                        }

                        // 2. clear comments
                        $row = trim(clearSQL($row, $isMultiLineComment));

                        // 3. parse delimiter row
                        if (preg_match('/^DELIMITER\s+[^ ]+/sUi', $row)) {
                            $delimiter = preg_replace('/^DELIMITER\s+([^ ]+)$/sUi', '$1', $row);
                            continue;
                        }

                        // 4. separate sql queries by delimiter
                        $offset = 0;
                        while (strpos($row, $delimiter, $offset) !== false) {
                            $delimiterOffset = strpos($row, $delimiter, $offset);
                            if (isQuoted($delimiterOffset, $row)) {
                                $offset = $delimiterOffset + strlen($delimiter);
                            } else {
                                $sql = trim($sql . ' ' . trim(substr($row, 0, $delimiterOffset)));
                                query($sql);

                                $row = substr($row, $delimiterOffset + strlen($delimiter));
                                $offset = 0;
                                $sql = '';
                            }
                        }
                        $sql = trim($sql . ' ' . $row);
                    }
                    if (strlen($sql) > 0) {
                        query($row);
                    }

                    fclose($file);
                }

                /**
                 * Remove comments from sql
                 *
                 * @param string sql
                 * @param boolean is multicomment line
                 * @return string
                 */
                function clearSQL($sql, &$isMultiComment)
                {
                    if ($isMultiComment) {
                        if (preg_match('#\*/#sUi', $sql)) {
                            $sql = preg_replace('#^.*\*/\s*#sUi', '', $sql);
                            $isMultiComment = false;
                        } else {
                            $sql = '';
                        }
                        if(trim($sql) == ''){
                            return $sql;
                        }
                    }

                    $offset = 0;
                    while (preg_match('{--\s|#|/\*[^!]}sUi', $sql, $matched, PREG_OFFSET_CAPTURE, $offset)) {
                        list($comment, $foundOn) = $matched[0];
                        if (isQuoted($foundOn, $sql)) {
                            $offset = $foundOn + strlen($comment);
                        } else {
                            if (substr($comment, 0, 2) == '/*') {
                                $closedOn = strpos($sql, '*/', $foundOn);
                                if ($closedOn !== false) {
                                    $sql = substr($sql, 0, $foundOn) . substr($sql, $closedOn + 2);
                                } else {
                                    $sql = substr($sql, 0, $foundOn);
                                    $isMultiComment = true;
                                }
                            } else {
                                $sql = substr($sql, 0, $foundOn);
                                break;
                            }
                        }
                    }
                    return $sql;
                }

                /**
                 * Check if "offset" position is quoted
                 *
                 * @param int $offset
                 * @param string $text
                 * @return boolean
                 */
                function isQuoted($offset, $text)
                {
                    if ($offset > strlen($text))
                        $offset = strlen($text);

                    $isQuoted = false;
                    for ($i = 0; $i < $offset; $i++) {
                        if ($text[$i] == "'")
                            $isQuoted = !$isQuoted;
                        if ($text[$i] == "\\" && $isQuoted)
                            $i++;
                    }
                    return $isQuoted;
                }

                function query($sql)
                {
                    global $mysqli;
                    //echo '#<strong>SQL CODE TO RUN:</strong><br>' . htmlspecialchars($sql) . ';<br><br>';
                    if (!$query = $mysqli->query($sql)) {
                        throw new Exception("Cannot execute request to the database {$sql}: " . $mysqli->error);
                    }
                }
        
        if((isset($_POST['username']) && $_POST['username'] != "") &&
           (isset($_POST['password']) && $_POST['password'] != "") &&
           (isset($_POST['dbhost']) && $_POST['dbhost'] != "") &&
           (isset($_POST['dbuser']) && $_POST['dbuser'] != "") &&
           (isset($_POST['dbpass'])) &&
           (isset($_POST['dbname']) && $_POST['dbname'] != "") 
          ) {
            $progress = 2;
        }else {
            $progress = 1;
        }


        if($progress == 2) { 
            $dbhost = $_POST['dbhost'];
            $dbuser = $_POST['dbuser'];
            $dbpass = $_POST['dbpass'];
            $dbname = $_POST['dbname'];
            
            $username = $_POST['username'];
            $password = $_POST['password'];
            
            
            set_time_limit(0);
            
            $mysqli = new mysqli($dbhost, $dbuser, $dbpass);
            $sql = "CREATE DATABASE IF NOT EXISTS $dbname;";
            if(mysqli_query($mysqli, $sql) === true) {
                $fl_1 = true;

                $mysqli->set_charset("utf8");
                mysqli_select_db($mysqli,$dbname );
//                sqlImport('dk_ct.sql');
                $time=date('Y-m-d H:i:s');
                $table = "CREATE TABLE `mcu` (
                      `id` int(11) NOT NULL,
                      `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                      `ip` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                      `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

                    CREATE TABLE `member` (
                      `id` int(11) NOT NULL,
                      `username` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                      `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                      `key_remember` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                      `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                      `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

                    CREATE TABLE `node_active` (
                      `id` int(11) NOT NULL,
                      `name` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
                      `status` int(11) NOT NULL,
                      `mcu_id` int(11) NOT NULL,
                      `pin_id` int(11) NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

                    CREATE TABLE `pin` (
                      `id` int(11) NOT NULL,
                      `pin_id` int(11) NOT NULL,
                      `node_id` int(11) NOT NULL,
                      `status` int(11) NOT NULL,
                      `switch` int(11) NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;


                    ALTER TABLE `mcu`
                      ADD PRIMARY KEY (`id`);

                    ALTER TABLE `member`
                      ADD PRIMARY KEY (`id`);

                    ALTER TABLE `node_active`
                      ADD PRIMARY KEY (`id`),
                      ADD KEY `MCU_ID` (`mcu_id`),
                      ADD KEY `PIN_ID` (`pin_id`);

                    ALTER TABLE `pin`
                      ADD PRIMARY KEY (`id`),
                      ADD KEY `node_id` (`node_id`);


                    ALTER TABLE `mcu`
                      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
                    ALTER TABLE `member`
                      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
                    ALTER TABLE `node_active`
                      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
                    ALTER TABLE `pin`
                      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

                    ALTER TABLE `node_active`
                      ADD CONSTRAINT `MCU_ID` FOREIGN KEY (`mcu_id`) REFERENCES `mcu` (`id`),
                      ADD CONSTRAINT `PIN_ID` FOREIGN KEY (`pin_id`) REFERENCES `pin` (`id`);

                    ALTER TABLE `pin`
                      ADD CONSTRAINT `node_id` FOREIGN KEY (`node_id`) REFERENCES `mcu` (`id`);
                    COMMIT;


                    INSERT INTO `member` (`id`, `username`, `password`, `key_remember`, `create_time`, `update_time`) VALUES (NULL, '$username', '".md5($password)."', '','$time', NULL);";
                if($query = mysqli_multi_query($mysqli,$table) === true) {
//                    echo "dddQ  ".$query;
                    $fl_2 = true;   
                    $fh = fopen("../install.lock", 'w') or die("Can't create file");
                    $f = fopen('../lib/config.php', 'w') or die('fopen failed');
                    $php_script= "<?php \n define('DBHOST','$dbhost');\n define('DBUSER','$dbuser');\n define('DBPASS','$dbpass');  \n define('DBNAME','$dbname');  \n ?>";

                    fwrite($f, $php_script);

                    fclose($f);
                }else {
                    $fl_2 = false;
                }

            }else {
                    $fl_1 = false;
            }
        ?>
            <div class="container margin-top-25">
            <div class="row justify-content-center">
                <div class="col-md-6 col-xs-8">
                    <div class="card">
                        <div class="card-header">
                            Setup file
                        </div>
                        <div class="card-block">
                           Installing...  <br />
                           <?php if($fl_1) echo "Create Database successfully"; ?> <br />
                           <?php if($fl_2) echo "Create Table successfully"; ?> <br />
                           <?php if($fl_2) echo "Create User info successfully"; ?> <br />
                           
                           <br />
                           <button class="btn btn-success" onClick="location.href='../'"> Next </button>
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php 
           
            
            


        }elseif($progress == 1) {
        ?>
        <div class="container margin-top-25">
            <div class="row justify-content-center">
                <div class="col-md-6 col-xs-8">
                    <div class="card">
                        <div class="card-header">
                            Setup file
                        </div>
                        <div class="card-block">
                            <form method="post" action="">
                            <div class="form-group">
                                <label>Hostname</label>
                                <input type="text" name="dbhost" class="form-control" placeholder="Enter Hostname">
                              </div>
                              <div class="form-group">
                                <label>DB Username</label>
                                <input type="text" name="dbuser"  class="form-control" placeholder="Enter Database username">
                              </div>
                              <div class="form-group">
                                <label>DB Password</label>
                                <input type="password" name="dbpass"  class="form-control" placeholder="Enter Database password">
                              </div>
                              <div class="form-group">
                                <label>DB name</label>
                                <input type="text" name="dbname"  class="form-control" placeholder="Enter Database name">
                              </div>
                             <hr />
                              <div class="form-group">
                                <label>Admin Username</label>
                                <input type="text" name="username"  class="form-control" placeholder="Enter Username">
                              </div>
                              <div class="form-group">
                                <label>Admin Password</label>
                                <input type="password" name="password"  class="form-control" placeholder="Enter Password">
                              </div>
                              <div class="form-group row justify-content-center">
                                <button class="btn btn-default">Install</button> &nbsp;&nbsp;
                                <button class="btn btn-default">Cancel</button>
                              </div>
                              
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        }
        ?>
    </body>

</html>