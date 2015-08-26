<?php
//Script to extract SWF files from pptx files (once unzipped)
//Set up file type an extension for checking</code>

$FWSMarker = "FWS";
$extension = ".BIN";

// Start parsing directory

if ($handle = opendir('.')) {
echo "Directory handle: $handle\n";
echo "Entries:\n";

/* Loop over the directory searching for all files */

while (false !== ($entry = readdir($handle))) {

/* Check for file extension*/

$filename = strtoupper(rtrim($entry));
$entry_len = strlen($entry);
$entry_pos = $entry_len - strlen($extension);

/* If .BIN search in file for FWS*/

if (substr($filename, $entry_pos , strlen($extension)) == $extension )
{
echo "Found entry $entry that is a .bin file \n";
$newhandle = fopen($entry, "r");
$contents = fread($newhandle, filesize($entry));
$marker = strpos($contents, $FWSMarker);
if ($marker != 0)
{
echo "FWS Found at position : $marker\n";

// truncate file from before FWS marker

$newlen = strlen ($contents);
$contents = substr($contents, $marker, ($newlen-$marker));

// get SWF file length from SWF header and reverse (little endian)

$swflen = substr ($contents, 4, 4);
$swflen = strrev($swflen);

//convert file length into something php can work with

         $act_len="";
         for ($i=0; $i<4; $i++){
          $newchar=ord(substr($swflen,$i,1));
          $newchar=strval(dechex($newchar));
          $act_len=$act_len.$newchar;
          }
         // convert from hex to dec
         $act_len=hexdec($act_len);
         echo "Actual SWF file length : $act_len bytes\n";
         /* Truncate File to that length*/
         $contents=substr($contents,0,$act_len);
         /* Save New File */
         $newfilename = $entry.".swf";
         $newhandle = fopen($newfilename, 'w');
         fwrite($newhandle,$contents);
         echo "SWF file outputted to $newfilename \n\n";
         fclose($newhandle);
                 }
        }
    }
   
//Wrap it up and exit
   
    closedir($handle);
}
?>