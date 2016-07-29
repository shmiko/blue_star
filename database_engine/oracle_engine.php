<?php

include_once("engine.php");

class OracleConnectionFactory extends ConnectionFactory {
    public function DoCreateConnection($AConnectionParams) {
        return new OracleConnection($AConnectionParams);
    }

    public function CreateDataset($AConnection, $sql) {
        return new OracleDataReader($AConnection, $sql);
    }

    function CreateEngCommandImp() {
        return new OracleEngCommandImp($this);
    }

    private $dateFormat = 'YYYY-MM-DD';
    private $dateTimeFormat = 'YYYY-MM-DD HH24:MI:SS';

    public function GetDateFormat() {
        return $this->dateFormat;
    }

    public function SetDateFormat($value) {
        $this->dateFormat = $value;
    }

    public function GetDateTimeFormat() {
        return $this->dateTimeFormat;
    }

    public function SetDateTimeFormat($value) {
        $this->dateTimeFormat = $value;
    }
}

class OracleEngCommandImp extends EngCommandImp {
    private function GetDateFormat() {
        return $this->GetConnectionFactory()->GetDateFormat();
    }

    private function GetDateTimeFormat() {
        return $this->GetConnectionFactory()->GetDateTimeFormat();
    }

    public function GetCaseSensitiveLikeExpression(FieldInfo $field, $filterValue) {
        return $this->CreateCaseSensitiveLikeExpression(
            $this->GetFieldFullName($field),
            $this->GetValueAsSQLString($filterValue)
        );
    }

    public function GetCastToCharExpression($value) {
        return sprintf("CAST(%s AS VARCHAR(4000))", $value);
    }


    public function GetCaseInsensitiveLikeExpression(FieldInfo $field, $filterValue) {
        return $this->CreateCaseInsensitiveLikeExpression(
            $this->GetFieldFullName($field),
            $this->GetValueAsSQLString($filterValue)
        );
    }

    public function QuoteIdentifier($identifier) {
        return '"' . $identifier . '"';
    }

    protected function GetDateFieldAsSQLForSelect($fieldInfo) {
        $result = sprintf('TO_CHAR(%s, \'%s\')', $this->GetFieldFullName($fieldInfo), $this->GetDateFormat());
        return $result;
    }

    protected function GetDateTimeFieldAsSQLForSelect($fieldInfo) {
        $result = sprintf('TO_CHAR(%s, \'%s\')', $this->GetFieldFullName($fieldInfo), $this->GetDateTimeFormat());
        return $result;
    }

    protected function GetDateFieldValueAsSQL($fieldInfo, $value) {
        return sprintf('TO_DATE(\'%s\', \'%s\')', $value->ToString('Y-m-d'), $this->GetDateFormat());
    }

    protected function GetDateTimeFieldValueAsSQL($fieldInfo, $value) {
        return sprintf('TO_TIMESTAMP(\'%s\', \'%s\')', $value->ToString('Y-m-d'), $this->GetDateTimeFormat());
    }

    public function GetFieldValueAsSQL($fieldInfo, $value) {
        if ($fieldInfo->FieldType == ftBlob && $value != null)
            return 'EMPTY_BLOB()';
        else
            return parent::GetFieldValueAsSQL($fieldInfo, $value);
    }

    public function GetLimitClause($limitCount, $upLimit) {
        return '';
    }

    public function GetAfterSelectSQL($command) {
        return 'ROWNUM AS MAESTRO_ROW_ID, ';
    }

    public function ExecuteInsertCommand($connection, $command) {
        $blobFieldTail = '';
        $blobFieldIndex = 0;
        $fieldValues = $command->GetFieldValues();

        foreach ($command->GetFields() as $field) {
            if ($field->FieldType == ftBlob && isset($fieldValues[$field->Name]) && $fieldValues[$field->Name] != null) {
                AddStr($blobFieldTail, sprintf('%s INTO :bind%s',
                    $this->QuoteIdentifier($field->Name),
                    $blobFieldIndex), ', ');
                $blobFieldIndex++;
            }
        }

        $resultSql = $command->GetSQL() . ($blobFieldTail != '' ? (' RETURNING ' . $blobFieldTail) : '');
        $statement = @oci_parse($connection->GetConnectionHandle(), $resultSql);

        $blobFieldIndex = 0;
        $blobDescriptors = array();
        foreach ($command->GetFields() as $field) {
            if ($field->FieldType == ftBlob && isset($fieldValues[$field->Name]) && $fieldValues[$field->Name] != null) {
                $blobDescriptors[$field->Name] = oci_new_descriptor($connection->GetConnectionHandle(), OCI_D_LOB);
                @oci_bind_by_name($statement, ':bind' . $blobFieldIndex, $blobDescriptors[$field->Name], -1, OCI_B_BLOB);
                $blobFieldIndex++;
            }
        }
        $result = @oci_execute($statement, OCI_COMMIT_ON_SUCCESS);

        foreach ($command->GetFields() as $field) {
            if ($field->FieldType == ftBlob && isset($fieldValues[$field->Name]) && $fieldValues[$field->Name] != null) {
                if (is_array($fieldValues[$field->Name]))
                    $blobDescriptors[$field->Name]->save(file_get_contents($fieldValues[$field->Name][0]));
                else
                    $blobDescriptors[$field->Name]->save($fieldValues[$field->Name]);

                $blobDescriptors[$field->Name]->free();
            }
        }

        $error = oci_error($statement);
        return $result;
    }

    public function ExecuteUpdateCommand($connection, $command) {
        $blobFieldTail = '';
        $blobFieldIndex = 0;
        $fieldValues = $command->GetValues();

        foreach ($command->GetFields() as $field) {
            if ($field->FieldType == ftBlob && isset($fieldValues[$field->Name])) {
                AddStr($blobFieldTail, sprintf('%s INTO :bind%s',
                    $this->QuoteIdentifier($field->Name),
                    $blobFieldIndex), ', ');
                $blobFieldIndex++;
            }
        }

        $resultSql = $command->GetSQL() . ($blobFieldTail != '' ? (' RETURNING ' . $blobFieldTail) : '');
        $statement = @oci_parse($connection->GetConnectionHandle(), $resultSql);

        $blobFieldIndex = 0;
        $blobDescriptors = array();
        foreach ($command->GetFields() as $field) {
            if ($field->FieldType == ftBlob && isset($fieldValues[$field->Name])) {
                $blobDescriptors[$field->Name] = oci_new_descriptor($connection->GetConnectionHandle(), OCI_D_LOB);
                @oci_bind_by_name($statement, ':bind' . $blobFieldIndex, $blobDescriptors[$field->Name], -1, OCI_B_BLOB);
                $blobFieldIndex++;
            }
        }
        $result = @oci_execute($statement, OCI_COMMIT_ON_SUCCESS);

        foreach ($command->GetFields() as $field) {
            if ($field->FieldType == ftBlob && isset($fieldValues[$field->Name])) {
                if (is_array($fieldValues[$field->Name]))
                    $blobDescriptors[$field->Name]->save(file_get_contents($fieldValues[$field->Name][0]));
                else
                    $blobDescriptors[$field->Name]->save($fieldValues[$field->Name]);
                $blobDescriptors[$field->Name]->free();
            }
        }

        $error = oci_error($statement);
        return $result;
    }

    protected function DoExecuteSelectCommand($connection, $command) {
        $upLimit = $command->GetUpLimit();
        $limitCount = $command->GetLimitCount();

        if (isset($upLimit) && isset($limitCount)) {

            $sql = sprintf('SELECT * FROM (SELECT RowNum as MAESTRO_ROWNUM, T.* FROM (%s) T) WHERE MAESTRO_ROWNUM BETWEEN %s AND %s',
                $command->GetSQL(),
                $upLimit + 1,
                $upLimit + $limitCount
            );
            $result = $this->GetConnectionFactory()->CreateDataset($connection, $sql);
            $result->Open();
            return $result;
        } else {
            return parent::DoExecuteSelectCommand($connection, $command);
        }
    }

    public function DoExecuteCustomSelectCommand($connection, $command) {
        $upLimit = $command->GetUpLimit();
        $limitCount = $command->GetLimitCount();

        if (isset($upLimit) && isset($limitCount)) {
            $sql = sprintf('SELECT * FROM (SELECT RowNum as MAESTRO_ROWNUM, T.* FROM (%s) T) WHERE MAESTRO_ROWNUM BETWEEN %s AND %s',
                $command->GetSQL(),
                $upLimit + 1,
                $upLimit + $limitCount
            );
            $result = $this->GetConnectionFactory()->CreateDataset($connection, $sql);
            $result->Open();
            return $result;
        } else {
            return parent::DoExecuteCustomSelectCommand($connection, $command);
        }
    }


}

class OracleConnection extends EngConnection {
    private $connectionHandle;
    private $lastStatement = null;

    protected function DoConnect() {
        if ($this->ConnectionParam('client_encoding') != '') {
            $this->connectionHandle = @oci_connect(
                $this->ConnectionParam('username'),
                $this->ConnectionParam('password'),
                $this->ConnectionParam('database'),
                $this->ConnectionParam('client_encoding'));

        } else {
            $this->connectionHandle = @oci_connect(
                $this->ConnectionParam('username'),
                $this->ConnectionParam('password'),
                $this->ConnectionParam('database'));
        }

        if (!$this->connectionHandle)
            return false;
        $this->ExecSQLEx("ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD'");
        $this->ExecSQLEx("ALTER SESSION SET NLS_TIMESTAMP_FORMAT='YYYY-MM-DD HH24:MI:SS'");
        return true;
    }

    protected function DoCreateDataReader($sql) {
        return new OracleDataReader($this, $sql);
    }

    public function IsDriverSupported() {
        return function_exists('oci_connect');
    }

    protected function DoGetDBMSName() {
        return 'Oracle';
    }

    protected function DoGetDriverExtensionName() {
        return 'oci';
    }

    protected function DoGetDriverInstallationLink() {
        return 'http://php.net/manual/en/oci8.installation.php';
    }


    protected function DoDisconnect() {
        oci_close($this->connectionHandle);
    }

    public function GetConnectionHandle() {
        return $this->connectionHandle;
    }

    protected function DoExecSQL($sql) {
        $this->lastStatement = @oci_parse($this->GetConnectionHandle(), $sql);
        return @oci_execute($this->lastStatement, OCI_COMMIT_ON_SUCCESS);
    }

    public function ExecScalarSQL($sql) {
        $statement = @oci_parse($this->GetConnectionHandle(), $sql);
        if ($statement) {
            if (@oci_execute($statement, OCI_COMMIT_ON_SUCCESS)) {
                $queryResult = oci_fetch_array($statement, OCI_NUM);
                return $queryResult[0];
            }
        }
        return null;
    }

    public function DoLastError() {
        if ($this->lastStatement) {
            $errorArray = oci_error($this->lastStatement);
        } else {
            if ($this->connectionHandle)
                $errorArray = oci_error($this->connectionHandle);
            else
                $errorArray = oci_error();
        }
        return $errorArray['message'];

    }

    protected function FormatConnectionParams() {
        return $this->ConnectionParam('username').'@'.$this->ConnectionParam('database');
    }

}

class OracleDataReader extends EngDataReader {
    /** @var null|resource */
    private $queryResult;
    private $lastFetchedRow;

    protected function FetchField() {
        echo "not supprted";
    }

    protected function FetchFields() {
        for ($i = 0; $i < oci_num_fields($this->queryResult); $i++)
            $this->AddField(oci_field_name($this->queryResult, $i + 1));
    }

    protected function DoOpen() {
        $this->queryResult = @oci_parse($this->GetConnection()->GetConnectionHandle(), $this->GetSQL());
        $result = @oci_execute($this->queryResult);
        if (!$result)
            $this->queryResult = null;
        return $result;
    }

    public function __construct($connection, $sql) {
        parent::__construct($connection, $sql);
        $this->queryResult = null;
    }

    public function Opened() {
        return $this->queryResult ? true : false;
    }

    public function Seek($rowIndex) {
        echo 'not supported';
    }

    public function Next() {
        $result = oci_fetch_array($this->queryResult, OCI_ASSOC + OCI_RETURN_NULLS + OCI_RETURN_LOBS);
        if (!$result)
            return false;
        for ($i = 1; $i <= oci_num_fields($this->queryResult); $i++) {
            $this->lastFetchedRow[oci_field_name($this->queryResult, $i)] =
                $result[oci_field_name($this->queryResult, $i)];
        }
        return true;
    }

    public function GetFieldValueByName($AFieldName) {
        return $this->GetActualFieldValue($AFieldName, $this->lastFetchedRow[$AFieldName]);
    }

    protected function LastError() {
        if ($this->queryResult)
            $errorArray = oci_error($this->queryResult);
        else
            $errorArray = parent::LastError();
        return $errorArray['message'];
    }
}
