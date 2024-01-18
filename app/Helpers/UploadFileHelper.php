<?php

function uploadToGCS($file,$customName = null,$directory) {
    $format = $file->extension();
    $customName ?? $customName = $file->getBasename();
    // $path = $file->storeAs($directory, $customName.'.'.$format, 'gcs'); //on
    $path = '/' . $directory . '/' . $customName.'.'.$format; // off
    return 'http://storage.googleapis.com/developer_dasarata'.$path;
}