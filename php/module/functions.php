<?php
//ID生成関数
function create_ID($file_path, $num, $id_name) {
    // 大文字小文字の英字と数字が混在する
    $id = str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz0123456789');
    // 先頭８桁をランダムパスワードとして使う
    $id = substr(str_shuffle($id), 0, $num);
    //重複しているidがあるかを探す
    if($fp = fopen($file_path, 'r')) {
        while($json = fgets($fp)) {
            $data = json_decode($json, true);
            if($data[$id_name] === $id) {
                create_id($file_path, $num, $id_name);
            }
        }
        fclose($fp);
        return $id;
    }
};

//削除モード
function delete_mode($file_path, $id_name) {
    $mode = isset($_POST['mode']) ? $_POST['mode'] : NULL;
    $id = isset($_POST['id']) ? $_POST['id'] : NULL;
    if($mode === "DELETE") {
        if($fp = fopen($file_path, 'r')) {
                if($fp) {
                    $array = array(); //削除で選択されたもの以外を配列に入れる
                    if(flock($fp, LOCK_EX | LOCK_NB)) {
                        while($json = fgets($fp)) {
                            $data = json_decode($json, true);
                            if($data[$id_name] === $id) {
                                unset($data);
                            } else {
                                if($data['title'] !== NULL || $data['comment'] !== NULL ||$data['body'] !== NULL) {
                                    $data_jsoncode = json_encode($data);
                                    array_push($array, $data_jsoncode);
                                }
                            }
                        }
                        if($fp = fopen($file_path, 'w')) {
                            //$arrayの中身を繰り返し処理
                            foreach($array as $val) {
                                if($fp) {
                                    if(flock($fp, LOCK_EX | LOCK_NB)) {
                                        if(fwrite($fp, $val . PHP_EOL) === false) {
                                            print('ファイル書き込みに失敗しました');
                                        }
                                        flock($fp, LOCK_UN);
                                    }
                                }
                            }
                            fclose($fp);
                        }
                        if(preg_match('/article/', $file_path)) {
                            $array = array();
                            //コメントもarticle_idが合致するものは削除
                            if($fp = fopen('./text/comment.txt', 'r')) {
                                if(flock($fp, LOCK_EX | LOCK_NB)) {
                                    while($json = fgets($fp)) {
                                        $data = json_decode($json, true);
                                        if($data[$id_name] === $id) {
                                            unset($data);
                                        } else {
                                            $data_jsoncode = json_encode($data);
                                            array_push($array, $data_jsoncode);
                                        }
                                    }
                                }
                                fclose($fp);
                            }
                            if($fp = fopen('./text/comment.txt', 'w')) {
                                //$arrayの中身を繰り返し処理
                                foreach($array as $val) {
                                    if($fp) {
                                        if(flock($fp, LOCK_EX | LOCK_NB)) {
                                            if(fwrite($fp, $val . PHP_EOL) === false) {
                                                print('ファイル書き込みに失敗しました');
                                            }
                                            flock($fp, LOCK_UN);
                                        }
                                    }
                                }
                                fclose($fp);
                            }
                        }
                    }
                }
            }
        fclose($fp);
    };
};