<?php
//------------------------------------------------------
$fileRequired = trailingslashit( 'writeTag' ) . 'WriteTag.php';
if( file_exists( $fileRequired ) ):
	require_once( $fileRequired );
endif;
//------------------------------------------------------
$fileRequired = trailingslashit( 'html2pdf' ) . 'html2pdf.php';
if( file_exists( $fileRequired ) ):
	require_once( $fileRequired );
endif;
//------------------------------------------------------
unset( $fileRequired );
//------------------------------------------------------