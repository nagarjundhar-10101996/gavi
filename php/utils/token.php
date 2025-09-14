<?php
function generateToken($len = 32) {
    return bin2hex(random_bytes($len/2));
}