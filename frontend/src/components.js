//layout
import NavigationDrawerList from './layout/NavigationDrawerList';
import NavigationDrawer from './layout/NavigationDrawer';
import AccountSettings from './layout/AccountSettings';
import SubPages from './layout/SubPages';

//COMPONENT
import FileUploader from './components/FileUploader';
import CurrencyFormat from './components/CurrencyFormat';
import DateFormat from './components/DateFormat';
import GlobalSelectBox from './components/select_box/GlobalSelectBox';
import CurrencyFormatV2 from './components/text_box/CurrencyFormatV2';

//STATIC COMPONENTS (SELECT BOX)
import GenderSelectBox from './components/select_box/static_select_box/GenderSelectBox';
import MaritalStatusSelectBox from './components/select_box/static_select_box/MaritalStatusSelectBox';
import RelationshipSelectBox from './components/select_box/static_select_box/RelationshipSelectBox';

//Company Center Popup
import CreateCompany from './views/company_center/popup/CreateCompany';
import ViewCompany from './views/company_center/popup/ViewCompany';
import EditCompany from './views/company_center/popup/EditCompany';
import ArchiveCompany from './views/company_center/popup/ArchiveCompany';
import ViewImageContract from './views/company_center/popup/ViewImageContract';
import ViewImageBenefitSchedule from './views/company_center/popup/ViewImageBenefitSchedule';

//Doctor Center Popup
import AddDoctor from './views/doctor_center/popup/AddDoctor';
import ViewDoctor from './views/doctor_center/popup/ViewDoctor';
import EditDoctor from './views/doctor_center/popup/EditDoctor';
import ArchiveDoctor from './views/doctor_center/popup/ArchiveDoctor';

//Member Center Popup
import CreateMember from './views/member_center/popup/CreateMember';
import TransactionMember from './views/member_center/popup/TransactionMember';
import ImportFileMember from './views/member_center/popup/ImportFileMember';
import ViewMember from './views/member_center/popup/ViewMember';
import EditMember from './views/member_center/popup/EditMember';
import ArchiveMember from './views/member_center/popup/ArchiveMember';

//Provider Center Popup
import CreateProvider from './views/provider_center/popup/CreateProvider';
import ViewProvider from './views/provider_center/popup/ViewProvider';
import EditProvider from './views/provider_center/popup/EditProvider';
import ImportProvider from './views/provider_center/popup/ImportProvider';
import ArchiveProvider from './views/provider_center/popup/ArchiveProvider';

//Access Level Popup
import CreatePosition from './views/settings/access_level/popup/CreatePosition';
import EditPosition from './views/settings/access_level/popup/EditPosition';
import ViewPosition from './views/settings/access_level/popup/ViewPosition';
import ArchivePosition from './views/settings/access_level/popup/ArchivePosition';
import PositionAccessChildren from './views/settings/access_level/misc/PositionAccessChildren';

//Admin Panel Popup
import CreateUserAdmin from './views/settings/admin_panel/popup/CreateUserAdmin';
import ArchiveUserAdmin from './views/settings/admin_panel/popup/ArchiveUserAdmin';
import EditUserAdmin from './views/settings/admin_panel/popup/EditUserAdmin';
import ViewUserAdmin from './views/settings/admin_panel/popup/ViewUserAdmin';

//Coverage Plan Popup
import CreateCoveragePlan from './views/settings/coverage_plan/popup/CreateCoveragePlan';
import CoverageSelectProcedure from './views/settings/coverage_plan/popup/CoverageSelectProcedure';
import ViewCoveragePlan from './views/settings/coverage_plan/popup/ViewCoveragePlan';
import ArchiveCoveragePlan from './views/settings/coverage_plan/popup/Archive';
// import ProcedureDescription from './views/settings/coverage_plan/popup/CoverageSelectProcedureDescription';

//Coverage Plan Description Popup
import CreatePlanDescription from './views/settings/maintenance/coverage_plan_description/popup/CreatePlanDescription';
import ImportPlanDescription from './views/settings/maintenance/coverage_plan_description/popup/ImportPlanDescription';
import ViewPlanDescription from './views/settings/maintenance/coverage_plan_description/popup/ViewPlanDescription';
import EditPlanDescription from './views/settings/maintenance/coverage_plan_description/popup/EditPlanDescription';
import ArchivePlanDescription from './views/settings/maintenance/coverage_plan_description/popup/ArchivePlanDescription';

import ImportPlanDescriptionPage from './views/settings/maintenance/coverage_plan_description/import/ImportPlanDescriptionPage';
import ImportMemberPage from './views/member_center/import/ImportMemberPage';
import ImportProviderPage from './views/provider_center/import/ImportProviderPage';


//Pre-existing Popup
import CreatePreExisting from './views/settings/maintenance/pre_existing/popup/CreatePreExisting';
import EditPreExisting from './views/settings/maintenance/pre_existing/popup/EditPreExisting';
import ArchivePreExisting from './views/settings/maintenance/pre_existing/popup/ArchivePreExisting';
import ViewPreExisting from './views/settings/maintenance/pre_existing/popup/ViewPreExisting';

//Pre-existing Popup
import CreatePaymentMethod from './views/settings/maintenance/payment_method/popup/CreatePaymentMethod';
import EditPaymentMethod from './views/settings/maintenance/payment_method/popup/EditPaymentMethod';
import ArchivePaymentMethod from './views/settings/maintenance/payment_method/popup/ArchivePaymentMethod';
import ViewPaymentMethod from './views/settings/maintenance/payment_method/popup/ViewPaymentMethod';

//procedures  type Popup
import CreateProceduresType from './views/settings/maintenance/procedures_type/popup/CreateProceduresType';
import ImportProceduresType from './views/settings/maintenance/procedures_type/popup/ImportProceduresType';
import ArchiveProceduresType from './views/settings/maintenance/procedures_type/popup/ArchiveProceduresType';
import EditProceduresType from './views/settings/maintenance/procedures_type/popup/EditProceduresType';
import ViewProceduresType from './views/settings/maintenance/procedures_type/popup/ViewProceduresType';

//procedures  type Popup
import CreateSpecialization from './views/settings/maintenance/specialization/popup/CreateSpecialization';
import ImportSpecialization from './views/settings/maintenance/specialization/popup/ImportSpecialization';
import ArchiveSpecialization from './views/settings/maintenance/specialization/popup/ArchiveSpecialization';
import EditSpecialization from './views/settings/maintenance/specialization/popup/EditSpecialization';
import ViewSpecialization from './views/settings/maintenance/specialization/popup/ViewSpecialization';

//Availment
import DisapprovedAvailment from './views/availment_center/popup/DisapprovedAvailment';

//diagnosis 
import CreateDiagnosis from './views/settings/maintenance/diagnosis_list/popup/CreateDiagnosis';
import EditDiagnosis from './views/settings/maintenance/diagnosis_list/popup/EditDiagnosis';
import ViewDiagnosis from './views/settings/maintenance/diagnosis_list/popup/ViewDiagnosis';
import ArchiveDiagnosis from './views/settings/maintenance/diagnosis_list/popup/ArchiveDiagnosis';

import MakeAsPending from './views/billing_center/page/popup/MakeAsPending';

//Audit trail
import ViewAuditTrail from './views/settings/audit_trail/popup/ViewAuditTrail';

//Payment Term
import CreatePaymentTerm from './views/settings/maintenance/payment_term/popup/CreatePaymentTerm';
import EditPaymentTerm from './views/settings/maintenance/payment_term/popup/EditPaymentTerm';
import ArchivePaymentTerm from './views/settings/maintenance/payment_term/popup/ArchivePaymentTerm';
import ViewPaymentTerm from './views/settings/maintenance/payment_term/popup/ViewPaymentTerm';

//Bank
import CreateBank from './views/settings/maintenance/bank/popup/CreateBank';
import ArchiveBank from './views/settings/maintenance/bank/popup/ArchiveBank';
import EditBank from './views/settings/maintenance/bank/popup/EditBank';
import ViewBank from './views/settings/maintenance/bank/popup/ViewBank';

//Member Template
import DownloadTemplateMember from './views/member_center/import/DownloadTemplateMember';

//Availment Template
import DownloadAvailmentTemplate from './views/availment_center/page/imports/DownloadAvailmentTemplate';

//Availment Summary
import CompanyTable from './views/reports/availment_summary/popup/SelectCompanyExport';


const Components = {
    CurrencyFormatV2,
    FileUploader,
    CurrencyFormat,
    DateFormat,
    GlobalSelectBox,
    GenderSelectBox,
    MaritalStatusSelectBox,
    RelationshipSelectBox,
    NavigationDrawerList,
    NavigationDrawer,
    AccountSettings,
    SubPages,
    CreateCompany,
    ViewCompany,
    EditCompany,
    ArchiveCompany,
    ViewImageContract,
    ViewImageBenefitSchedule,
    AddDoctor,
    ViewDoctor,
    EditDoctor,
    ArchiveDoctor,
    CreateMember,
    TransactionMember,
    ImportFileMember,
    ViewMember,
    EditMember,
    ArchiveMember,
    CreateProvider,
    ViewProvider,
    EditProvider,
    ImportProvider,
    ArchiveProvider,
    CreatePosition,
    EditPosition,
    ViewPosition,
    ArchivePosition,
    PositionAccessChildren,
    CreateUserAdmin,
    ArchiveUserAdmin,
    EditUserAdmin,
    ViewUserAdmin,
    CreateCoveragePlan,
    CoverageSelectProcedure,
    CreatePlanDescription,
    ImportPlanDescription,
    ViewCoveragePlan,
    ArchiveCoveragePlan,
    ViewPlanDescription,
    EditPlanDescription,
    ArchivePlanDescription,   
    CreatePreExisting,
    EditPreExisting,
    ArchivePreExisting, 
    ViewPreExisting,
    CreateProceduresType,
    ImportProceduresType,
    ArchiveProceduresType,
    EditProceduresType,
    ViewProceduresType,
    CreatePaymentMethod,
    EditPaymentMethod,
    ArchivePaymentMethod,
    ViewPaymentMethod,
    ImportPlanDescriptionPage,
    ImportMemberPage,
    ImportProviderPage,
    CreateDiagnosis,
    CreateSpecialization,
    EditSpecialization,
    ArchiveSpecialization,
    ViewSpecialization,
    EditDiagnosis,
    ViewDiagnosis,
    ArchiveDiagnosis,
    DisapprovedAvailment,
    MakeAsPending,
    //Audit Trail
    ViewAuditTrail,
    CreatePaymentTerm,
    EditPaymentTerm,
    ArchivePaymentTerm,
    ViewPaymentTerm,
    CreateBank,
    ArchiveBank,
    EditBank,
    ViewBank,
    //Template Member
    DownloadTemplateMember,
    //Template Availment
    DownloadAvailmentTemplate,
    //Availment Summary
    CompanyTable,
};

export default Components;
