<!DOCTYPE html>
<html lang="ru">
    <head>
        <title><?= $appTitle ?></title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <link rel="stylesheet" href="/public/main.css">
        <script src="/public/main.js"></script>
    </head>

    <body>
        <div class="overlay">
            <video autoplay="" muted="" loop="">
                <source src="/public/video/demo.mp4" type="video/mp4">
            </video>
        </div>
        <div class="c-container" id="bg">
            <header>
                <div class="header-box">
                    <?php if (!empty($_SESSION['auth'])) { ?>
                        <div class="logout">
                            <form method="POST">
                                <button title="Разлогиниться" id="unauthorize" name="unauthorize" type="submit" value="1">
                                    <img alt="logout" src="./public/images/logout.png">
                                </button>
                                <span></span>
                            </form>
                        </div>
                    <?php } ?>
                    <div class="app-title">
                        <h1 id="h1"><?= $appName ?></h1>
                    </div>
                </div>
            </header>
            <div class="text-animated-box">
                <span id="t1">system</span>
                <span id="t2">power</span>
                <span id="t3">is off</span>
                <span id="t4">now</span>
                <span id="t5">--------</span>
            </div>
            <div class="main">
                <?php if (!empty($_SESSION['auth']) && empty($errors['system'])) { ?>
                    <div class="container dots">
                        <?
                            $html = '<h2>Массив случайных чисел 5 на 7 (числа от 0 до 1000)</h2>';
                            $summOveral = 0;
                            $summString = [];
                            $summColumn = [];
                            $html.= '<table>';
                            for ($i = 0; $i <= 4; $i++) {
                                if (!isset($summString[$i])) {
                                    $summString[$i] = 0;
                                }
                                $html.=  '<tr>';
                                for ($y = 0; $y <= 6; $y++) {
                                    if (!isset($summColumn[$y])) {
                                        $summColumn[$y] = 0;
                                    }
                                    $number = rand(1, 1000);
                                    $summString[$i] = $summString[$i] + $number;
                                    $summColumn[$y] = $summColumn[$y] + $number;
                                    $summOveral = $summOveral + $number;
                                    $html.= '<td class="number">' . $number . '</td>';
                                }
                                $html.= '<td>' . $summString[$i] . '</td>';
                                $html.= '</tr>';
                            }
                            $html.=  '<tr>';
                            for ($y = 0; $y <= 6; $y++) {
                                $html.=  '<td>' . $summColumn[$y] . '</td>';
                            }
                            $html.= '<td></td></tr>';
                            $html.= '<tr><td colspan="8"><b>Сумма:</b> ' . $summOveral . '</td></tr></table>';
                            $html.= '<br>';
                        ?>
                        <?=$html?>
                    </div>
                    <div class="container dots">
                        <?php
                            $html = '<h2>Лесенка цифр</h2>';
                            $html.= '<table>';
                            $columnsCounter = 0;
                            function echoNumbersAsStairs($number, $lineCounter, $stopNumber, &$html, &$columnsCounter)
                            {
                                $enumber = $number;
                                for ($i = 1; $i <= $lineCounter; $i++) {
                                    if ($enumber > $stopNumber) {
                                        break;
                                    }
                                    if ($enumber < 10) {
                                        $html.= '<td class="number">' . $enumber . '</td>';
                                    } else {
                                        $html.= '<td class="number">' . $enumber . '</td>';
                                    }
                                    $columnsCounter++;
                                    $enumber++;
                                }
                                return $enumber - 1;
                            }

                            $stopNumber = 100;
                            $lineCounter = 0;
                            for ($i = 1; $i <= $stopNumber; $i++) {
                                $lineCounter++;
                                $html.= '<tr>';
                                $i = echoNumbersAsStairs($i, $lineCounter, $stopNumber, $html, $columnsCounter);
                                $html.= '<td colspan="' . 14 - $columnsCounter . '"></td>';
                                $columnsCounter = 0;
                                $html.= '</tr>';
                            }
                            
                            $html.= '</table>';
                        ?>
                        <?=$html?>
                    </div>
                    <div class="container dots">
                        <h2>Числа Фибоначчи</h2>
                        
                        <?php
                            $arr = [];
                            
                            for ($x = 0; $x < 6; $x++) {
                                for ($y = 0; $y < 6; $y++) {
                                    if (!isset($arr[$y - 1][$x]) || !isset($arr[$y - 2][$x])) {
                                        if ($x > 0 && $y == 0) {
                                            $arr[$y][$x] = $arr[5][$x - 1] + $arr[4][$x - 1];
                                        } elseif ($x > 0 && $y == 1) {
                                            $arr[$y][$x] = $arr[5][$x - 1] + $arr[0][$x];
                                        } elseif ($x == 0 && $y == 0) {
                                            $arr[$y][$x] = 0;
                                        } else {
                                            $arr[$y][$x] = 1;
                                        }
                                    } else {
                                        $arr[$y][$x] = $arr[$y - 1][$x] + $arr[$y - 2][$x];
                                    }
                                }
                            }
                    
                            $diagonalSumm = 0;
                            foreach ($arr as $key => $value) {
                                $diagonalSumm = $diagonalSumm + $arr[5 - $key][$key];
                            }

                            $html = '<table>';
                            foreach($arr as $keya => $valuea) {
                                $html.= '<tr>';
                                foreach($valuea as $keyb => $valueb) {
                                    $html.= '<td class="number">' . $valueb . '</td>';
                                }
                                $html.='</tr>';
                            }
                            $html.= '<tr><td colspan="6">Сумма цифр по диагонали: ' . $diagonalSumm .'</td></tr>';
                            $html.= '</table>';
                        ?>
                        <?=$html?>
                    </div>
                    <div class="container dots">
                        <h2>Пример запроса к внешнему API</h2>
                        <p>Узнаем город по IP адресу</p>
                        <p>URL API: <i>http://ip-api.com/json/</i></p>
                        <p>IP адрес (задан фиксированный): <i>24.48.0.1</i></p>
                        <p>Результат запроса (JSON):</p>
                        <i>
                        <?php
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, 'http://ip-api.com/json/24.48.0.1');
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                            $requestData = curl_exec($ch);
                            curl_close($ch);
                            if (!empty($requestData)) {
                                var_dump($requestData);
                            }
                        ?>
                        </i>
                    </div>
                    <div class="container dots">
                        <?php
                            /**
                             * Read string part
                             * 
                             * @return array
                             */
                            function readStringPart($string = '', $position = 0)
                            {
                                $result = [
                                    'position' => $position,
                                    'result' => ''
                                ];

                                $patternType = checkIsPattern($string, $position);
                                if (!empty($patternType)) {
                                    $patternNumber = getPatternNumber($string, $position, strlen(getPatterns($patternType)));
                                }

                                switch ($patternType) {
                                    case 1: { //pattern 1 rules
                                        return readStringPart($string, $patternNumber);
                                    }
                                    case 2: { //pattern 2 rules
                                        return readStringPart($string, $position + $patternNumber + strlen($patternNumber) + strlen(getPatterns($patternType)));
                                    }
                                    case 3: { // pattern 3 rules
                                        return readStringPart($string, $position - $patternNumber + strlen($patternNumber) + strlen(getPatterns($patternType)));
                                    }
                                    default: {
                                        break;
                                    }
                                }

                                $result['result'] = mb_substr($string, $position, 1);
                                
                                return $result;
                            }

                            /**
                             * Check if string combination is pattern, returns pattern array key or 0
                             * 
                             * @var $string string
                             * @var $position int
                             * 
                             * @return int
                             */
                            function checkIsPattern($string = '', $position = 0) 
                            {
                                foreach (getPatterns() as $patternKey => $patternValue) {
                                    if (mb_substr($string, $position, mb_strlen($patternValue)) == $patternValue) {
                                        return $patternKey;
                                    }
                                }

                                return 0;
                            }

                            /**
                             * Array of patterns
                             * 
                             * @var int $key
                             * 
                             * @return mixed
                             */
                            function getPatterns($key = 0) 
                            {
                                $patternsArr = [
                                    1 => '->',
                                    2 => '+',
                                    3 => '-'
                                ];

                                if (!empty($key)) {
                                    return $patternsArr[$key];
                                }

                                return $patternsArr; 
                            }

                            /**
                             * Get pattern arguments
                             * 
                             * @return int
                             */
                            function getPatternNumber($string, $patternPosition = 0, $skipCounter = 0) 
                            {
                                $numberStr = '';

                                for ($i = $skipCounter; $i<=10; $i++) {
                                    $str = mb_substr($string, $patternPosition + $i, 1);
                                    if (!is_numeric($str)) {
                                        break;
                                    }
                                    $numberStr.= $str;
                                }

                                return (int)$numberStr;
                            }

                            /**
                             * String decryption example
                             */
                            $string = '->11гI+20∆∆I+4µcњiL->5•Ћ®†Ѓ p+5f-7Ќ¬A pro+10g+1悦ra->58->44m+1*m+2a喜er!';

                            $resultString = '';
                            $stringLength = mb_strlen($string);

                            $strPosition = 0;
                            $counter = 0;
                            while (true) {
                                $stepResults = readStringPart($string, $strPosition);
                                $strPosition = $stepResults['position'] + 1;
                                $resultString.= $stepResults['result'];
                                $counter++;
                                if ($strPosition >= mb_strlen($string)) {
                                    break;
                                }
                            }
                        ?>
                        <h2>Декодирование строки на php</h2>
                        <p>Исходная строка: «->11гI+20∆∆I+4µcњiL->5•Ћ®†Ѓ p+5f-7Ќ¬A pro+10g+1悦ra->58->44m+1*m+2a喜er!»</p>
                        <p>Результат: «<?=$resultString?>»</p>
                    </div>
                <?php } ?>
            </div>
            <canvas id="fbg"></canvas>
            <footer>
                <div class="footer-icon">
                    <div class="squares">
                        <div id="square1"></div>
                        <div id="square2"></div>
                        <div id="square3"></div>
                    </div>
                    <img alt="kartoshkin" src="/public/images/kartoshkin.png">
                    <br>
                    <a href="mailto:iksoc@vk.com">iksoc@vk.com</a>
                </div>
                <?php foreach ($errors['system'] as $error) { ?>
                    <div class="message error"><?php echo $error; ?></div>
                <?php } ?>
                <?php foreach ($messages['sysinfo'] as $message) { ?>
                    <div class="message"><?php echo $message; ?></div>
                <?php } ?>
                <img class="q-triangle" alt="quadrat-triangle" src="/public/images/q-triangle.png">
            </footer>
        </div>
    </body>
</html>