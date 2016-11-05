<?php
    // be aware of file / directory permissions on your server
    $img_name ='order'.md5(time()).rand(383,1000).'.jpg';
    $dst_img_name = '../img/order/'.$img_name;

    move_uploaded_file($_FILES['webcam']['tmp_name'], $dst_img_name);
    echo $img_name;
?>
