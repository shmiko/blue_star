<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                   ATTENTION!
 * If you see this message in your browser (Internet Explorer, Mozilla Firefox, Google Chrome, etc.)
 * this means that PHP is not properly installed on your web server. Please refer to the PHP manual
 * for more details: http://php.net/manual/install.php 
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 */


    include_once dirname(__FILE__) . '/' . 'components/utils/check_utils.php';
    CheckPHPVersion();
    CheckTemplatesCacheFolderIsExistsAndWritable();


    include_once dirname(__FILE__) . '/' . 'phpgen_settings.php';
    include_once dirname(__FILE__) . '/' . 'database_engine/oracle_engine.php';
    include_once dirname(__FILE__) . '/' . 'components/page.php';


    function GetConnectionOptions()
    {
        $result = GetGlobalConnectionOptions();
        $result['client_encoding'] = 'AL32UTF8';
        GetApplication()->GetUserAuthorizationStrategy()->ApplyIdentityToConnectionOptions($result);
        return $result;
    }

    
    
    // OnBeforePageExecute event handler
    
    
    
    class CGU_STOCKPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $selectQuery = 'SELECT * FROM tbl_AdminData
            ORDER BY OrderNum,Pickslip Asc';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $this->dataset = new QueryDataset(
              new OracleConnectionFactory(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'CGU STOCK');
            $field = new StringField('CUSTOMER');
            $this->dataset->AddField($field, false);
            $field = new StringField('PARENT');
            $this->dataset->AddField($field, false);
            $field = new StringField('COSTCENTRE');
            $this->dataset->AddField($field, false);
            $field = new StringField('ORDERNUM');
            $this->dataset->AddField($field, false);
            $field = new StringField('ORDERWARENUM');
            $this->dataset->AddField($field, false);
            $field = new StringField('CUSTREF');
            $this->dataset->AddField($field, false);
            $field = new StringField('PICKSLIP');
            $this->dataset->AddField($field, false);
            $field = new StringField('PICKNUM');
            $this->dataset->AddField($field, false);
            $field = new StringField('DESPATCHNOTE');
            $this->dataset->AddField($field, false);
            $field = new StringField('DESPATCHDATE');
            $this->dataset->AddField($field, false);
            $field = new StringField('FEETYPE');
            $this->dataset->AddField($field, false);
            $field = new StringField('ITEM');
            $this->dataset->AddField($field, false);
            $field = new StringField('DESCRIPTION');
            $this->dataset->AddField($field, false);
            $field = new IntegerField('QTY');
            $this->dataset->AddField($field, true);
            $field = new StringField('UOI');
            $this->dataset->AddField($field, false);
            $field = new IntegerField('UNITPRICE');
            $this->dataset->AddField($field, true);
            $field = new IntegerField('OW_UNIT_SELL_PRICE');
            $this->dataset->AddField($field, true);
            $field = new IntegerField('SELL_EXCL');
            $this->dataset->AddField($field, true);
            $field = new IntegerField('SELL_EXCL_TOTAL');
            $this->dataset->AddField($field, true);
            $field = new IntegerField('SELL_INCL');
            $this->dataset->AddField($field, true);
            $field = new IntegerField('SELL_INCL_TOTAL');
            $this->dataset->AddField($field, true);
            $field = new IntegerField('REPORTINGPRICE');
            $this->dataset->AddField($field, true);
            $field = new StringField('ADDRESS');
            $this->dataset->AddField($field, false);
            $field = new StringField('ADDRESS2');
            $this->dataset->AddField($field, false);
            $field = new StringField('SUBURB');
            $this->dataset->AddField($field, false);
            $field = new StringField('STATE');
            $this->dataset->AddField($field, false);
            $field = new StringField('POSTCODE');
            $this->dataset->AddField($field, false);
            $field = new StringField('DELIVERTO');
            $this->dataset->AddField($field, false);
            $field = new StringField('ATTENTIONTO');
            $this->dataset->AddField($field, false);
            $field = new IntegerField('WEIGHT');
            $this->dataset->AddField($field, true);
            $field = new IntegerField('PACKAGES');
            $this->dataset->AddField($field, true);
            $field = new IntegerField('ORDERSOURCE');
            $this->dataset->AddField($field, true);
            $field = new StringField('ILNOTE2');
            $this->dataset->AddField($field, false);
            $field = new StringField('NILOCN');
            $this->dataset->AddField($field, false);
            $field = new IntegerField('NIAVAILACTUAL');
            $this->dataset->AddField($field, true);
            $field = new IntegerField('COUNTOFSTOCKS');
            $this->dataset->AddField($field, true);
            $field = new StringField('EMAIL');
            $this->dataset->AddField($field, false);
            $field = new StringField('BRAND');
            $this->dataset->AddField($field, false);
        }
    
        protected function CreatePageNavigator()
        {
            $result = new CompositePageNavigator($this);
            
            $partitionNavigator = new PageNavigator('pnav', $this, $this->dataset);
            $partitionNavigator->SetRowsPerPage(20);
            $result->AddPageNavigator($partitionNavigator);
            
            return $result;
        }
    
        public function GetPageList()
        {
            $currentPageCaption = $this->GetShortCaption();
            $result = new PageList($this);
            if (GetCurrentUserGrantForDataSource('CGU STOCK')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('CGU STOCK'), 'CGU_STOCK.php', $this->RenderText('LUX_STOCK'), $currentPageCaption == $this->RenderText('CGU STOCK')));
            
            if ( HasAdminPage() && GetApplication()->HasAdminGrantForCurrentUser() )
              $result->AddPage(new PageLink($this->GetLocalizerCaptions()->GetMessageString('AdminPage'), 'phpgen_admin.php', $this->GetLocalizerCaptions()->GetMessageString('AdminPage'), false, true));
            return $result;
        }
    
        protected function CreateRssGenerator()
        {
            return null;
        }
    
        protected function CreateGridSearchControl(Grid $grid)
        {
            $grid->UseFilter = false;
    
        }
    
        protected function CreateGridAdvancedSearchControl(Grid $grid)
        {
    
        }
    
        protected function AddOperationsColumns(Grid $grid)
        {
            $actionsBandName = 'actions';
            $grid->AddBandToBegin($actionsBandName, $this->GetLocalizerCaptions()->GetMessageString('Actions'), true);
            if ($this->GetSecurityInfo()->HasViewGrant())
            {
                $column = new RowOperationByLinkColumn($this->GetLocalizerCaptions()->GetMessageString('View'), OPERATION_VIEW, $this->dataset);
                $grid->AddViewColumn($column, $actionsBandName);
            }
        }
    
        protected function AddFieldColumns(Grid $grid)
        {
    
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
    
        }
    
        protected function AddEditColumns(Grid $grid)
        {
    
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
    
            if ($this->GetSecurityInfo()->HasAddGrant())
            {
                $grid->SetShowAddButton(false);
                $grid->SetShowInlineAddButton(false);
            }
            else
            {
                $grid->SetShowInlineAddButton(false);
                $grid->SetShowAddButton(false);
            }
        }
    
        protected function AddPrintColumns(Grid $grid)
        {
    
        }
    
        protected function AddExportColumns(Grid $grid)
        {
    
        }
    
        public function GetPageDirection()
        {
            return null;
        }
    
        protected function ApplyCommonColumnEditProperties(CustomEditColumn $column)
        {
            $column->SetShowSetToNullCheckBox(false);
    		$column->SetVariableContainer($this->GetColumnVariableContainer());
        }
    
        function GetCustomClientScript()
        {
            return ;
        }
        
        function GetOnPageLoadedClientScript()
        {
            return ;
        }
    
        protected function CreateGrid()
        {
            $result = new Grid($this, $this->dataset, 'CGU_STOCKGrid');
            if ($this->GetSecurityInfo()->HasDeleteGrant())
               $result->SetAllowDeleteSelected(false);
            else
               $result->SetAllowDeleteSelected(false);   
            
            ApplyCommonPageSettings($this, $result);
            
            $result->SetUseImagesForActions(false);
            $result->SetUseFixedHeader(false);
            
            $result->SetShowLineNumbers(false);
            
            $result->SetHighlightRowAtHover(false);
            $result->SetWidth('');
            $this->CreateGridSearchControl($result);
            $this->CreateGridAdvancedSearchControl($result);
            $this->AddOperationsColumns($result);
            $this->AddFieldColumns($result);
            $this->AddSingleRecordViewColumns($result);
            $this->AddEditColumns($result);
            $this->AddInsertColumns($result);
            $this->AddPrintColumns($result);
            $this->AddExportColumns($result);
    
            $this->SetShowPageList(true);
            $this->SetHidePageListByDefault(false);
            $this->SetExportToExcelAvailable(false);
            $this->SetExportToWordAvailable(false);
            $this->SetExportToXmlAvailable(false);
            $this->SetExportToCsvAvailable(false);
            $this->SetExportToPdfAvailable(false);
            $this->SetPrinterFriendlyAvailable(false);
            $this->SetSimpleSearchAvailable(true);
            $this->SetAdvancedSearchAvailable(false);
            $this->SetFilterRowAvailable(false);
            $this->SetVisualEffectsEnabled(false);
            $this->SetShowTopPageNavigator(true);
            $this->SetShowBottomPageNavigator(true);
    
            //
            // Http Handlers
            //
    
            return $result;
        }
        
        public function OpenAdvancedSearchByDefault()
        {
            return false;
        }
    
        protected function DoGetGridHeader()
        {
            return '';
        }
    }



    try
    {
        $Page = new CGU_STOCKPage("CGU_STOCK.php", "CGU_STOCK", GetCurrentUserGrantForDataSource("CGU STOCK"), 'UTF-8');
        $Page->SetShortCaption('CGU STOCK');
        $Page->SetHeader(GetPagesHeader());
        $Page->SetFooter(GetPagesFooter());
        $Page->SetCaption('LUX_STOCK');
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("CGU STOCK"));
        GetApplication()->SetEnableLessRunTimeCompile(GetEnableLessFilesRunTimeCompilation());
        GetApplication()->SetCanUserChangeOwnPassword(
            !function_exists('CanUserChangeOwnPassword') || CanUserChangeOwnPassword());
        GetApplication()->SetMainPage($Page);
        GetApplication()->Run();
    }
    catch(Exception $e)
    {
        ShowErrorPage($e->getMessage());
    }
	
