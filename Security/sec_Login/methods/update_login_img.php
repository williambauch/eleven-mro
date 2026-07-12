<?php

function update_login_img($key_val, $img_val)
{
    $databaseType = strtolower($this->Ini->nm_tpbanco);
    $nm_bases_lob_geral = array_merge(
        $this->Ini->nm_bases_oracle, 
        $this->Ini->nm_bases_ibase, 
        $this->Ini->nm_bases_informix, 
        $this->Ini->nm_bases_mysql, 
        $this->Ini->nm_bases_access, 
        $this->Ini->nm_bases_sqlite, 
        $this->Ini->nm_bases_db2,
    	$this->Ini->nm_bases_postgres,
        ['pdo_sqlsrv']
    );

    // Tratamento de $img_val
    if (in_array($databaseType, $this->Ini->nm_bases_access)) {
        $nm_tmp = preg_replace('/\s+/', '', $img_val);

        if (is_string($img_val) && substr($img_val, 0, 4) !== "*nm*" && substr($nm_tmp, 0, 4) === "*nm*") {
            $img_val = $nm_tmp;
        }

        if (!empty($img_val) && $img_val !== 'null' && substr($img_val, 0, 4) !== "*nm*") {
            $img_val = "*nm*" . base64_encode($img_val);
        }
    } elseif (in_array($databaseType, $this->Ini->nm_bases_mssql) && $databaseType !== "pdo_sqlsrv") {
        if (!empty($img_val) && $img_val !== 'null' && substr($img_val, 0, 4) !== "*nm*") {
            $img_val = "*nm*" . base64_encode($img_val);
        }
    } elseif (!in_array($databaseType, $nm_bases_lob_geral)) {
        $img_val = substr($this->Db->qstr($img_val), 1, -1);
    }

    // Determinação do valor de $temp_img
    $temp_img = "''";
    if ($img_val === "" && in_array($databaseType, $this->Ini->nm_bases_access)) {
        $temp_img = "null";
    } elseif (!in_array($databaseType, $nm_bases_lob_geral)) {
        $temp_img = "'" . $img_val . "'";
    } elseif (in_array($databaseType, array_merge(
        $this->Ini->nm_bases_ibase, 
        $this->Ini->nm_bases_mysql, 
        $this->Ini->nm_bases_access
    ))) {
        $temp_img = "''";
    } elseif (in_array($databaseType, $this->Ini->nm_bases_informix)) {
        $temp_img = "null";
    } elseif (!in_array($databaseType, array_merge($this->Ini->nm_bases_sqlite, $this->Ini->nm_bases_postgres))) {
        $temp_img = "empty_blob()";
    }

    // Comando de atualização
    $comando = "UPDATE public.sec_users SET picture = $temp_img WHERE login = " . $this->Db->qstr($key_val);
    $rs = $this->Db->Execute($comando);

    if ($rs === false) {
        return $this->Db->ErrorMsg();
    }

    // Atualização de blob para bases específicas
    if (in_array($databaseType, $nm_bases_lob_geral)) {
        $rs = $this->Db->UpdateBlob("public.sec_users", "picture", $img_val, "login = " . $this->Db->qstr($key_val));
        if ($rs === false) {
            return $this->Db->ErrorMsg();
        }
    }

    return 'ok';
}
