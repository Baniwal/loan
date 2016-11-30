<?php
class NoHashComponent extends Object {
    /**
     * 暗号化しないでそのまま返す
     */
    function hashPasswords($data){
        return $data;
    }
}
?>