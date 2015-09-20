<?php
/** 
* library/documents.inc.php Functions used in the documents request for patient portal. 
* 
* 
* 
* Copyright (C) 2015 Terry Hill <terry@lillysystems.com> 
* 
* LICENSE: This program is free software; you can redistribute it and/or 
* modify it under the terms of the GNU General Public License 
* as published by the Free Software Foundation; either version 3 
* of the License, or (at your option) any later version. 
* This program is distributed in the hope that it will be useful, 
* but WITHOUT ANY WARRANTY; without even the implied warranty of 
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
* GNU General Public License for more details. 
* You should have received a copy of the GNU General Public License 
* along with this program. If not, see <http://opensource.org/licenses/gpl-license.php>;. 
* 
* @package OpenEMR 
* @author Terry Hill <terry@lillysystems.com>
* @link http://www.open-emr.org 
*/

	function get_extension($imagetype) {
		if(empty($imagetype)) return false;
		switch($imagetype)
		{
			case 'application/andrew-inset': return '.ez';
			case 'application/mac-binhex40': return '.hqx';
			case 'application/mac-compactpro': return '.cpt';
			case 'application/msword': return '.doc';
			case 'application/octet-stream': return '.bin';
			case 'application/octet-stream': return '.dms';
			case 'application/octet-stream': return '.lha';
			case 'application/octet-stream': return '.lzh';
			case 'application/octet-stream': return '.exe';
			case 'application/octet-stream': return '.class';
			case 'application/octet-stream': return '.so';
			case 'application/octet-stream': return '.dll';
			case 'application/oda': return '.oda';
			case 'application/pdf': return '.pdf';
			case 'application/postscript': return '.ai';
			case 'application/postscript': return '.eps';
			case 'application/postscript': return '.ps';
			case 'application/smil': return '.smi';
			case 'application/smil': return '.smil';
			case 'application/vnd.wap.wbxml': return '.wbxml';
			case 'application/vnd.wap.wmlc': return '.wmlc';
			case 'application/vnd.wap.wmlscriptc': return '.wmlsc';
			case 'application/x-bcpio': return '.bcpio';
			case 'application/x-cdlink': return '.vcd';
			case 'application/x-chess-pgn': return '.pgn';
			case 'application/x-cpio': return '.cpio';
			case 'application/x-csh': return '.csh';
			case 'application/x-director': return '.dcr';
			case 'application/x-director': return '.dir';
			case 'application/x-director': return '.dxr';
			case 'application/x-dvi': return '.dvi';
			case 'application/x-futuresplash': return '.spl';
			case 'application/x-gtar': return '.gtar';
			case 'application/x-hdf': return '.hdf';
			case 'application/x-javascript': return '.js';
			case 'application/x-koan': return '.skp';
			case 'application/x-koan': return '.skd';
			case 'application/x-koan': return '.skt';
			case 'application/x-koan': return '.skm';
			case 'application/x-latex': return '.latex';
			case 'application/x-netcdf': return '.nc';
			case 'application/x-netcdf': return '.cdf';
			case 'application/x-sh': return '.sh';
			case 'application/x-shar': return '.shar';
			case 'application/x-shockwave-flash': return '.swf';
			case 'application/x-stuffit': return '.sit';
			case 'application/x-sv4cpio': return '.sv4cpio';
			case 'application/x-sv4crc': return '.sv4crc';
			case 'application/x-tar': return '.tar';
			case 'application/x-tcl': return '.tcl';
			case 'application/x-tex': return '.tex';
			case 'application/x-texinfo': return '.texinfo';
			case 'application/x-texinfo': return '.texi';
			case 'application/x-troff': return '.t';
			case 'application/x-troff': return '.tr';
			case 'application/x-troff': return '.roff';
			case 'application/x-troff-man': return '.man';
			case 'application/x-troff-me': return '.me';
			case 'application/x-troff-ms': return '.ms';
			case 'application/x-ustar': return '.ustar';
			case 'application/x-wais-source': return '.src';
			case 'application/xhtml+xml': return '.xhtml';
			case 'application/xhtml+xml': return '.xht';
			case 'application/zip': return '.zip';
			case 'audio/basic': return '.au';
			case 'audio/basic': return '.snd';
			case 'audio/midi': return '.mid';
			case 'audio/midi': return '.midi';
			case 'audio/midi': return '.kar';
			case 'audio/mpeg': return '.mpga';
			case 'audio/mpeg': return '.mp2';
			case 'audio/mpeg': return '.mp3';
			case 'audio/x-aiff': return '.aif';
			case 'audio/x-aiff': return '.aiff';
			case 'audio/x-aiff': return '.aifc';
			case 'audio/x-mpegurl': return '.m3u';
			case 'audio/x-pn-realaudio': return '.ram';
			case 'audio/x-pn-realaudio': return '.rm';
			case 'audio/x-pn-realaudio-plugin': return '.rpm';
			case 'audio/x-realaudio': return '.ra';
			case 'audio/x-wav': return '.wav';
			case 'chemical/x-pdb': return '.pdb';
			case 'chemical/x-xyz': return '.xyz';
			case 'image/bmp': return '.bmp';
			case 'image/gif': return '.gif';
			case 'image/ief': return '.ief';
			case 'image/jpeg': return '.jpeg';
			case 'image/jpeg': return '.jpg';
			case 'image/jpeg': return '.jpe';
			case 'image/png': return '.png';
			case 'image/tiff': return '.tiff';
			case 'image/tif': return '.tif';
			case 'image/vnd.djvu': return '.djvu';
			case 'image/vnd.djvu': return '.djv';
			case 'image/vnd.wap.wbmp': return '.wbmp';
			case 'image/x-cmu-raster': return '.ras';
			case 'image/x-portable-anymap': return '.pnm';
			case 'image/x-portable-bitmap': return '.pbm';
			case 'image/x-portable-graymap': return '.pgm';
			case 'image/x-portable-pixmap': return '.ppm';
			case 'image/x-rgb': return '.rgb';
			case 'image/x-xbitmap': return '.xbm';
			case 'image/x-xpixmap': return '.xpm';
			case 'image/x-windowdump': return '.xwd';
			case 'model/iges': return '.igs';
			case 'model/iges': return '.iges';
			case 'model/mesh': return '.msh';
			case 'model/mesh': return '.mesh';
			case 'model/mesh': return '.silo';
			case 'model/vrml': return '.wrl';
			case 'model/vrml': return '.vrml';
			case 'text/css': return '.css';
			case 'text/html': return '.html';
			case 'text/html': return '.htm';
			case 'text/plain': return '.asc';
			case 'text/plain': return '.txt';
			case 'text/richtext': return '.rtx';
			case 'text/rtf': return '.rtf';
			case 'text/sgml': return '.sgml';
			case 'text/sgml': return '.sgm';
			case 'text/tab-seperated-values': return '.tsv';
			case 'text/vnd.wap.wml': return '.wml';
			case 'text/vnd.wap.wmlscript': return '.wmls';
			case 'text/x-setext': return '.etx';
			case 'text/xml': return '.xml';
			case 'text/xml': return '.xsl';
			case 'video/mpeg': return '.mpeg';
			case 'video/mpeg': return '.mpg';
			case 'video/mpeg': return '.mpe';
			case 'video/quicktime': return '.qt';
			case 'video/quicktime': return '.mov';
			case 'video/vnd.mpegurl': return '.mxu';
			case 'video/x-msvideo': return '.avi';
			case 'video/x-sgi-movie': return '.movie';
			case 'x-conference-xcooltalk': return '.ice';
			default: return "";
		}
	}
    
?>