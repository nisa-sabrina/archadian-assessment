<?php
$filePicked = new Func("filePicked", function ($oEvent = null) use (&$parseExcel) {
    $oFile = get(get(get($oEvent, "target"), "files"), 0.0);
    $sFilename = get($oFile, "name");
    call($parseExcel, $oFile);
});
$copy = new Func("copy", function () use (&$document, &$alert) {
    $copyText = call_method($document, "getElementById", "ediholder");
    call_method($copyText, "select");
    call_method($copyText, "setSelectionRange", 0.0, 99999.0);
    call_method($document, "execCommand", "copy");
    call($alert, "Text Copied!");
});
$get_date_str = new Func("get_date_str", function ($d = null, $type = null) use (&$String) {
    $now = $d;
    $dt = call_method($now, "getDate");
    $dt = get(call($String, $dt), "length") < 2.0 ? _plus(call($String, "0"), call($String, $dt)) : $dt;
    $hrs = call_method($now, "getHours");
    $hrs = get(call($String, $hrs), "length") < 2.0 ? _plus(call($String, "0"), call($String, $hrs)) : $hrs;
    $min = call_method($now, "getMinutes");
    $min = get(call($String, $min), "length") < 2.0 ? _plus(call($String, "0"), call($String, $min)) : $min;
    $sec = call_method($now, "getSeconds");
    $sec = get(call($String, $sec), "length") < 2.0 ? _plus(call($String, "0"), call($String, $sec)) : $sec;
    $mth = _plus(call_method($now, "getMonth"), 1.0);
    $mth = get(call($String, $mth), "length") < 2.0 ? _plus(call($String, "0"), call($String, $mth)) : $mth;
    if (eq($type, "daterawonly")) {
        return _concat(call_method($now, "getFullYear"), "", call($String, $mth), "", call($String, $dt));
    } else if (eq($type, "timetominrawonly")) {
        return _concat(call($String, $hrs), "", call($String, $min));
    } else {
        return _concat(call_method($now, "getFullYear"), "", call($String, $mth), "", call($String, $dt), "", call($String, $hrs), "", call($String, $min), "", call($String, $sec));
    }
});
$CSVtoArray = new Func("CSVtoArray", function ($text = null) use (&$undefined) {
    $re_valid = new RegExp("^\\s*(?:'[^'\\\\]*(?:\\\\[\\S\\s][^'\\\\]*)*'|\"[^\"\\\\]*(?:\\\\[\\S\\s][^\"\\\\]*)*\"|[^,'\"\\s\\\\]*(?:\\s+[^,'\"\\s\\\\]+)*)\\s*(?:,\\s*(?:'[^'\\\\]*(?:\\\\[\\S\\s][^'\\\\]*)*'|\"[^\"\\\\]*(?:\\\\[\\S\\s][^\"\\\\]*)*\"|[^,'\"\\s\\\\]*(?:\\s+[^,'\"\\s\\\\]+)*)\\s*)*\$", "");
    $re_value = new RegExp("(?!\\s*\$)\\s*(?:'([^'\\\\]*(?:\\\\[\\S\\s][^'\\\\]*)*)'|\"([^\"\\\\]*(?:\\\\[\\S\\s][^\"\\\\]*)*)\"|([^,'\"\\s\\\\]*(?:\\s+[^,'\"\\s\\\\]+)*))\\s*(?:,|\$)", "g");
    if (not(call_method($re_valid, "test", $text))) {
        return Object::$null;
    }
    $a = new Arr();
    call_method($text, "replace", $re_value, new Func(function ($m0 = null, $m1 = null, $m2 = null, $m3 = null) use (&$undefined, &$a) {
        if ($m1 !== $undefined) {
            call_method($a, "push", call_method($m1, "replace", new RegExp("\\\\'", "g"), "'"));
        } else if ($m2 !== $undefined) {
            call_method($a, "push", call_method($m2, "replace", new RegExp("\\\\\"", "g"), "\""));
        } else if ($m3 !== $undefined) {
            call_method($a, "push", $m3);
        }


        return "";
    }));
    if (is(call_method(new RegExp(",\\s*\$", ""), "test", $text))) {
        call_method($a, "push", "");
    }
    return $a;
});
$cleanString = new Func("cleanString", function ($input = null) {
    $output = "";
    for ($i = 0.0; $i < get($input, "length"); $i++) {
        if (call_method($input, "charCodeAt", $i) <= 127.0) {
            $output = _plus($output, call_method($input, "charAt", $i));
        }
    }
    return $output;
});
$parseExcel = new Func(function ($file = null) use (&$FileReader, &$XLSX, &$Date, &$get_date_str, &$«24», &$CSVtoArray, &$cleanString, &$console) {
    $reader = _new($FileReader);
    set($reader, "onload", new Func(function ($e = null) use (&$XLSX, &$Date, &$get_date_str, &$«24», &$CSVtoArray, &$cleanString) {
        $data = get(get($e, "target"), "result");
        $workbook = call_method($XLSX, "read", $data, new Object("type", "binary"));
        call_method(get($workbook, "SheetNames"), "forEach", new Func(function ($sheetName = null) use (&$XLSX, &$workbook, &$Date, &$get_date_str, &$«24», &$CSVtoArray, &$cleanString) {
            $XL_row_object = call_method(get($XLSX, "utils"), "sheet_to_csv", get(get($workbook, "Sheets"), $sheetName));
            $line = 0.0;
            $contcount = 0.0;
            $allRows = call_method($XL_row_object, "split", new RegExp("\\r?\\n|\\r", ""));
            $dt = _new($Date);
            $refno = call($get_date_str, $dt, "");
            $edi = _concat("UNB+UNOA:2+KMT+", call_method(call($«24», "#recv_code"), "val"), "+", call($get_date_str, $dt, "daterawonly"), ":", call($get_date_str, $dt, "timetominrawonly"), "+", $refno, "'\n");
            $edi = _plus($edi, _concat("UNH+", $refno, "+COPRAR:D:00B:UN:SMDG21+LOADINGCOPRAR'\n"));
            $line++;
            $report_dt = "";
            $voyage = "";
            $vslname = "";
            $callsign = "";
            $opr = "";
            for ($singleRow = 0.0; $singleRow < get($allRows, "length"); $singleRow++) {
                if ($singleRow > 6.0) {
                    break;
                }
                $rowCells = call_method(get($allRows, $singleRow), "split", ",");
                if (eq($singleRow, 1.0)) {
                    $tmpdt = call_method(get($rowCells, 1.0), "split", "/");
                    $day = get($tmpdt, 0.0);
                    $month = get($tmpdt, 1.0);
                    $tmpyear = call_method(get($tmpdt, 2.0), "split", " ");
                    $report_date = _new($Date, _concat(get($tmpyear, 0.0), "-", $month, "-", $day, " ", get($tmpyear, 1.0)));
                    $report_dt = call($get_date_str, $report_date, "");
                }
                if (eq($singleRow, 3.0)) {
                    if (!eq(_typeof(get($rowCells, 3.0)), "undefined")) {
                        $tmp = call_method(get($rowCells, 3.0), "split", "/");
                        $voyage = get($tmp, 0.0);
                        $callsign = get($tmp, 1.0);
                        $opr = get($tmp, 2.0);
                        $vslname = get($rowCells, 1.0);
                    }
                }
            }
            $edi = _plus($edi, _concat("BGM+45+", $report_dt, "+5'\n"));
            $line++;
            $edi = _plus($edi, _concat("TDT+20+", $voyage, "+1++172:", $opr, "+++", call_method(call($«24», "#callsign_code"), "val"), ":103::", $vslname, "'\n"));
            $line++;
            $edi = _plus($edi, _concat("RFF+VON:", $voyage, "'\n"));
            $line++;
            $edi = _plus($edi, _concat("NAD+CA+", $opr, "'\n"));
            $line++;
            for ($singleRow = 0.0; $singleRow < get($allRows, "length"); $singleRow++) {
                if (!eq(_typeof(get($allRows, $singleRow)), "undefined")) {
                    $rowCells = call($CSVtoArray, get($allRows, $singleRow));
                    if ($singleRow > 7.0) {
                        $contcount++;
                        $fe = "5";
                        if (!eq(_typeof(get($rowCells, 3.0)), "undefined") && eq(get($rowCells, 3.0), "E")) {
                            $fe = "4";
                        }
                        $type = "2";
                        if (!eq(_typeof(get($rowCells, 11.0)), "undefined") && eq(get($rowCells, 11.0), "Y")) {
                            $type = "6";
                        }
                        if (!eq(_typeof(get($rowCells, 1.0)), "undefined") && !eq(_typeof(get($rowCells, 7.0)), "undefined")) {
                            $edi = _plus($edi, _concat("EQD+CN+", get($rowCells, 1.0), "+", get($rowCells, 7.0), ":102:5++", $type, "+", $fe, "'\n"));
                            $line++;
                        }
                        if (!eq(_typeof(get($rowCells, 6.0)), "undefined")) {
                            $edi = _plus($edi, _concat("LOC+11+", get($rowCells, 5.0), ":139:6'\n"));
                            $line++;
                        }
                        if (!eq(_typeof(get($rowCells, 6.0)), "undefined")) {
                            $edi = _plus($edi, _concat("LOC+7+", get($rowCells, 6.0), ":139:6'\n"));
                            $line++;
                        }
                        if (!eq(_typeof(get($rowCells, 19.0)), "undefined")) {
                            $edi = _plus($edi, _concat("LOC+9+", get($rowCells, 19.0), ":139:6'\n"));
                            $line++;
                        }
                        if (!eq(_typeof(get($rowCells, 13.0)), "undefined")) {
                            $edi = _plus($edi, _concat("MEA+AAE+VGM+KGM:", get($rowCells, 13.0), "'\n"));
                            $line++;
                        }
                        if (!eq(_typeof(get($rowCells, 17.0)), "undefined") && !eq(call_method($«24», "trim", get($rowCells, 17.0)), "") && !eq(call_method($«24», "trim", get($rowCells, 17.0)), "/")) {
                            $tmp = call_method(get($rowCells, 17.0), "split", ",");
                            for ($i = 0.0; $i < get($tmp, "length"); $i++) {
                                $dim = call_method(get($rowCells, 17.0), "split", "/");
                                if (eq(call_method($«24», "trim", get($dim, 0.0)), "OF")) {
                                    $edi = _plus($edi, _concat("DIM+5+CMT:", call_method($«24», "trim", get($dim, 1.0)), "'\n"));
                                    $line++;
                                }
                                if (eq(call_method($«24», "trim", get($dim, 0.0)), "OB")) {
                                    $edi = _plus($edi, _concat("DIM+6+CMT:", call_method($«24», "trim", get($dim, 1.0)), "'\n"));
                                    $line++;
                                }
                                if (eq(call_method($«24», "trim", get($dim, 0.0)), "OR")) {
                                    $edi = _plus($edi, _concat("DIM+7+CMT::", call_method($«24», "trim", get($dim, 1.0)), "'\n"));
                                    $line++;
                                }
                                if (eq(call_method($«24», "trim", get($dim, 0.0)), "OL")) {
                                    $edi = _plus($edi, _concat("DIM+8+CMT::", call_method($«24», "trim", get($dim, 1.0)), "'\n"));
                                    $line++;
                                }
                                if (eq(call_method($«24», "trim", get($dim, 0.0)), "OH")) {
                                    $edi = _plus($edi, _concat("DIM+9+CMT:::", call_method($«24», "trim", get($dim, 1.0)), "'\n"));
                                    $line++;
                                }
                            }
                        }
                        if (!eq(_typeof(get($rowCells, 15.0)), "undefined") && !eq(call_method($«24», "trim", get($rowCells, 15.0)), "") && !eq(call_method($«24», "trim", get($rowCells, 15.0)), "/")) {
                            $temperature = get($rowCells, 15.0);
                            $temperature = call_method($temperature, "replace", " ", "");
                            $temperature = call_method($temperature, "replace", "C", "");
                            $temperature = call_method($temperature, "replace", "+", "");
                            $edi = _plus($edi, _concat("TMP+2+", $temperature, ":CEL'\n"));
                            $line++;
                        }
                        if (!eq(_typeof(get($rowCells, 25.0)), "undefined") && !eq(call_method($«24», "trim", get($rowCells, 25.0)), "") && !eq(call_method($«24», "trim", get($rowCells, 25.0)), "/")) {
                            $tmp = call_method(get($rowCells, 25.0), "split", ",");
                            if (eq(get($tmp, 0.0), "L")) {
                                $edi = _plus($edi, _concat("SEL+", get($tmp, 1.0), "+CA'\n"));
                                $line++;
                            }
                            if (eq(get($tmp, 0.0), "S")) {
                                $edi = _plus($edi, _concat("SEL+", get($tmp, 1.0), "+SH'\n"));
                                $line++;
                            }
                            if (eq(get($tmp, 0.0), "M")) {
                                $edi = _plus($edi, _concat("SEL+", get($tmp, 1.0), "+CU'\n"));
                                $line++;
                            }
                        }
                        if (!eq(_typeof(get($rowCells, 8.0)), "undefined")) {
                            $edi = _plus($edi, _concat("FTX+AAI+++", get($rowCells, 8.0), "'\n"));
                            $line++;
                        }
                        if (!eq(_typeof(get($rowCells, 12.0)), "undefined") && !eq(call_method($«24», "trim", get($rowCells, 12.0)), "") && !eq(call_method($«24», "trim", get($rowCells, 12.0)), "/")) {
                            $edi = _plus($edi, _concat("FTX+AAA+++", call_method($«24», "trim", call($cleanString, get($rowCells, 12.0))), "'\n"));
                            $line++;
                        }
                        if (!eq(_typeof(get($rowCells, 18.0)), "undefined") && !eq(call_method($«24», "trim", get($rowCells, 18.0)), "") && !eq(call_method($«24», "trim", get($rowCells, 18.0)), "/")) {
                            $edi = _plus($edi, _concat("FTX+HAN++", get($rowCells, 18.0), "'\n"));
                            $line++;
                        }
                        if (!eq(_typeof(get($rowCells, 14.0)), "undefined") && !eq(get($rowCells, 14.0), "") && !eq(call_method($«24», "trim", get($rowCells, 14.0)), "/")) {
                            $tmp = call_method(get($rowCells, 14.0), "split", "/");
                            $edi = _plus($edi, _concat("DGS+IMD+", get($tmp, 0.0), "+", get($tmp, 1.0), "'\n"));
                            $line++;
                        }
                        if (!eq(_typeof(get($rowCells, 2.0)), "undefined") && !eq(call_method($«24», "trim", get($rowCells, 2.0)), "")) {
                            $edi = _plus($edi, _concat("NAD+CF+", get($rowCells, 2.0), ":160:ZZZ'\n"));
                            $line++;
                        }
                    }
                }
            }
            $contcount--;
            $edi = _plus($edi, _concat("CNT+16:", $contcount, "'\n"));
            $line++;
            $line++;
            $edi = _plus($edi, _concat("UNT+", $line, "+", $refno, "'\n"));
            $edi = _plus($edi, _concat("UNZ+1+", $refno, "'"));
            call_method(call($«24», "#my_file_output"), "val", $edi);
        }));
    }));
    set($reader, "onerror", new Func(function ($ex = null) use (&$console) {
        call_method($console, "log", $ex);
    }));
    call_method($reader, "readAsBinaryString", $file);
});
call($«24», new Func(function () use (&$oFileIn, &$document, &$filePicked) {
    $oFileIn = call_method($document, "getElementById", "my_file_input");
    if (is(get($oFileIn, "addEventListener"))) {
        call_method($oFileIn, "addEventListener", "change", $filePicked, false);
    }
}));
