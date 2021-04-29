<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Manage Employee
          <b-button
            @click="openModalEmployee"
            type="button"
            v-b-tooltip.hover
            title="Create new Employee"
            class="btn btn-success btn-labeled pull-right margin-right-10"
            v-if="roles.create_employee"
          >
            <i class="fas fa-plus-square"></i>
          </b-button>

          <b-button
            @click="updateLog"
            type="button"
            v-b-tooltip.hover
            title="Update BIO logs"
            class="btn btn-success btn-labeled pull-right margin-right-10"
            v-if="roles.update_RAlog"
          >
            <i class="fas fa-pen-fancy"></i>
          </b-button>
          <hrReport></hrReport>
        </p>
      </div>

      <div class="elClr panel-body">
        <div>
          <b-row style="margin:10px;">
            <b-col md="5" class="my-1">
              <b-form-group label-cols-sm="2" label="Filter" class="mb-0">
                <b-input-group>
                  <b-form-input v-model="tblFilter" placeholder="Filter"></b-form-input>
                  <b-input-group-append>
                    <b-button :disabled="!tblFilter" @click="tblFilter = ''">Clear</b-button>
                  </b-input-group-append>
                </b-input-group>
              </b-form-group>
            </b-col>
            <b-col md="5 " class="my-1">
              <button
                v-b-modal="'modalEmployeeFilter'"
                type="button"
                class="btn btn-success btn-labeled pull-right margin-left-10"
              >Multiple Filter</button>
            </b-col>

            <b-col md="2 " class="my-1">
              <b-form-group label-cols-sm="4" label="Show" class="mb-0">
                <b-form-select v-model="perPage" :options="pageOptions"></b-form-select>
              </b-form-group>
            </b-col>
          </b-row>
          <b-table
            ref="selectableTable"
            selectable
            @row-selected="onRowSelected"
            class="elClr"
            show-empty
            striped
            hover
            outlined
            :fields="fields"
            :items="items"
            :filter="tblFilter"
            :busy="tblisBusy"
            :current-page="currentPage"
            :per-page="perPage"
            :tbody-tr-class="tblRowClass"
            head-variant=" elClr"
            @filtered="onFiltered"
            @row-clicked="tblRowClicked"
          >
            <div slot="table-busy" class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>
            <template slot="table-caption"></template>

            <template v-slot:cell(actions)="row">
              <button
                class="btn btn-success btn-labeled"
                v-b-tooltip.hover
                title="Manage Leave Balance"
                @click="openModalLeave(row.item)"
                v-if="roles.manage_leave"
              >
                <i class="fas fa-plane-departure"></i>
              </button>
              <button
                class="btn btn-success btn-labeled"
                v-b-tooltip.hover
                title="Manage Schedule"
                @click="openModalSched(row.item)"
                v-if="roles.view_dtr"
              >
                <i class="fas fa-calendar-alt"></i>
              </button>
              <button
                class="btn btn-success btn-labeled"
                v-b-tooltip.hover
                title="Manage Approver"
                @click="openModalApprover(row.item)"
                v-if="roles.view_approver"
              >
                <i class="fas fa-user-check"></i>
              </button>
              <button
                class="btn btn-success btn-labeled"
                v-b-tooltip.hover
                title="Manage Earnings"
                @click="openModal(row.item, 'modalEarning')"
                v-if="roles.earnings"
              >
                <i class="fas fa-angle-double-up"></i>
              </button>

              <button
                class="btn btn-success btn-labeled"
                v-b-tooltip.hover
                title="Manage Deductions"
                @click="openModal(row.item, 'modalDeduction')"
                v-if="roles.deduction"
              >
                <i class="fas fa-angle-double-down"></i>
              </button>

              <button
                class="btn btn-success btn-labeled"
                v-b-tooltip.hover
                title="Manage Payslip"
                @click="openModal(row.item , 'ModalManagePayslip')"
                v-if="roles.view_payslip"
              >
                <i class="fas fa-money-check"></i>
              </button>
            </template>
            <template v-slot:cell(chkbox)="row">
              <div>
                <p-check
                  @change="chkbox_clicked(row)"
                  class="p-icon p-curve p-jelly"
                  color="primary"
                  v-model="row.rowSelected"
                >
                  <!-- v-model="row.item.chk" -->
                  <i slot="extra" class="icon fas fa-check"></i>
                </p-check>
              </div>
            </template>
          </b-table>
          <div
            v-if="
              roles.create_leave || roles.create_dtr || roles.create_payslip
            "
          >
            <div class="row">
              <div class="col-md-1">
                <img src="/src/img/arrow_ltr.png" style="padding-left:14px" />
              </div>

              <div class="col-md-2">
                <div style="margin-left:-20px">
                  <p-check
                    @change="selectAllRows($event)"
                    class="p-icon p-curve p-jelly"
                    color="primary"
                  >
                    <i slot="extra" class="icon fas fa-check"></i>
                  </p-check>Check all
                </div>
              </div>
              <div class="col-md-5" v-if="item_selected.length > 0">
                <div style="margin-left:-50px">
                  <button
                    class="btn btn-success"
                    v-b-tooltip.hover
                    title="Add Leave Balance to all selected employee"
                    v-b-modal="'ModalAddLeave'"
                    v-if="roles.create_leave"
                  >
                    <i class="fas fa-plane-departure"></i>
                  </button>
                  <button
                    class="btn btn-success"
                    v-b-tooltip.hover
                    v-b-modal="'ModalAddSched'"
                    title="Add Schedule to all selected employee"
                    v-if="roles.create_dtr"
                  >
                    <i class="fas fa-calendar-alt"></i>
                  </button>
                  <!-- <button
                    class="btn btn-success"
                    v-b-tooltip.hover
                    title="Add Payslip to all selected employee"
                    v-if="roles.create_payslip"
                  >
                    <i class="fas fa-money-check"></i>
                  </button>-->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="elClr panel-footer">
        <div class="row" style="background-color:; padding:15px;">
          <div class="col-md-8" style="background-color:;">
            <span class="elClr">{{ totalRows }} item/s found.</span>
          </div>

          <div class="col-md-4" style="background-color:;">
            <b-pagination
              v-model="currentPage"
              :total-rows="totalRows"
              :per-page="perPage"
              class="my-0 pull-right"
            ></b-pagination>
          </div>
        </div>
      </div>

      <!-- ModelAddEmployee ---------------------------------------------------------------------------------------->
      <b-modal
        id="ModelAddEmployee"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        ok-only
      >
        <template v-slot:modal-title>
          <p v-if="item_edit.bioID == ''">Add Employee</p>
          <p v-if="item_edit.bioID != ''">Update Employee</p>
        </template>

        <!-- form -->

        <div class="rowFields mx-auto row" v-if="item_edit.bioID == ''">
          <div class="col-lg-3">
            <p class="textLabel">Biometric ID:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="bio_id"
              ref="bio_id"
              class="form-control"
              v-b-tooltip.hover
              title="Biometric ID of the Employee"
              placeholder="Biometric ID"
              v-validate="'required'"
              v-model.trim="item_edit.id"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('bio_id')"
            >Biometric ID is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">First Name:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="fname"
              ref="fname"
              class="form-control"
              v-b-tooltip.hover
              title="First name of the Employee"
              placeholder="First name"
              v-validate="'required'"
              v-model.trim="item_edit.first_name"
              autocomplete="off"
              autofocus="on"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('fname')"
            >First Name is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Middle Name:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="mname"
              ref="mname"
              class="form-control"
              v-b-tooltip.hover
              title="Middle name of the Employee"
              placeholder="Middle name"
              v-model.trim="item_edit.middle_name"
              autocomplete="off"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Last Name:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="lname"
              ref="lname"
              class="form-control"
              v-b-tooltip.hover
              title="Last name of the Employee"
              placeholder="Last name"
              v-validate="'required'"
              v-model.trim="item_edit.last_name"
              autocomplete="off"
            />
            <small class="text-danger pull-left" v-show="errors.has('lname')">Last Name is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Gender:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="genders"
              v-model="item_edit.gender"
              option-value="name"
              option-text="name"
              placeholder="Select Gender"
              name="genders"
              v-validate="'required'"
            ></model-list-select>
            <small class="text-danger pull-left" v-show="errors.has('genders')">Gender is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Date Hire:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              <date-picker
                v-model="item_edit.date_hired"
                :config="Dateoptions"
                v-b-tooltip.hover
                title="Date Hire of the employee"
                placeholder="Date Hire (yyyy-MM-dd)"
                autocomplete="off"
                v-validate="'required'"
                name="date_hired"
              ></date-picker>
            </div>
            <small
              class="text-danger pull-left"
              v-show="errors.has('date_hired')"
            >Date Hired is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Group:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="groups"
              v-model="item_edit.group_id"
              option-value="id"
              option-text="name"
              placeholder="Select Group"
              name="groups"
              v-validate="'required'"
            ></model-list-select>
            <small class="text-danger pull-left" v-show="errors.has('groups')">Group is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Rate:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="rates"
              v-model="item_edit.rate_id"
              option-value="id"
              option-text="name"
              placeholder="Select Rate"
              name="rates"
              v-validate="'required'"
            ></model-list-select>
            <small class="text-danger pull-left" v-show="errors.has('rates')">Rate is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Position:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="positions"
              v-model="item_edit.position_id"
              option-value="id"
              option-text="name"
              placeholder="Select Position"
              name="positions"
              v-validate="'required'"
            ></model-list-select>
            <small
              class="text-danger pull-left"
              v-show="errors.has('positions')"
            >Position is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Branch:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="branches"
              v-model="item_edit.branch_id"
              option-value="id"
              option-text="name"
              placeholder="Select Branch"
              name="branches"
              v-validate="'required'"
            ></model-list-select>
            <small class="text-danger pull-left" v-show="errors.has('branches')">Branch is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Department:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="departments"
              v-model="item_edit.department_id"
              option-value="id"
              option-text="name"
              placeholder="Select Department"
              name="departments"
              v-validate="'required'"
            ></model-list-select>
            <small
              class="text-danger pull-left"
              v-show="errors.has('departments')"
            >Department is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Employment Status:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="employment_statuses"
              v-model="item_edit.employment_status"
              option-value="name"
              option-text="name"
              placeholder="Select Employment Status"
              name="employment_statuses"
              v-validate="'required'"
            ></model-list-select>
            <small
              class="text-danger pull-left"
              v-show="errors.has('employment_statuses')"
            >Employment Status is required.</small>
          </div>
        </div>

        <!-- Other Details------------------------------------------------------------------------------ -->
        <hr />
        <div class="rowFields mx-auto row">
          <div class="col-lg-12">
            <center>
              <a
                href="javascript:void(0);"
                @click="view_other_details = true"
                v-if="!view_other_details"
              >Show Other Details (Optional)</a>
              <a
                href="javascript:void(0);"
                @click="view_other_details = false"
                v-if="view_other_details"
              >Hide Other Details</a>
            </center>
          </div>
        </div>
        <div class="other_details" id="other_details" v-if="view_other_details">
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Civil Status:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-b-tooltip.hover
                title="Civil status of the Employee"
                placeholder="Civil status"
                v-model.trim="item_edit.civil_status"
                autocomplete="off"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Permanent Address:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-b-tooltip.hover
                title="Permanent address of the Employee"
                placeholder="Permanent address"
                v-model.trim="item_edit.permanent_address"
                autocomplete="off"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Telephone #:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-b-tooltip.hover
                title="Telephone number of the Employee"
                placeholder="Telephone number"
                v-model.trim="item_edit.tel_no"
                autocomplete="off"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Mobile #:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-b-tooltip.hover
                title="Mobile number of the Employee"
                placeholder="Mobile number"
                v-model.trim="item_edit.mobile_no"
                autocomplete="off"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Email 1:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-b-tooltip.hover
                title="Email of the Employee"
                placeholder="Email"
                v-model.trim="item_edit.email1"
                autocomplete="off"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Email 2:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-b-tooltip.hover
                title="Second email of the Employee"
                placeholder="Second Email"
                v-model.trim="item_edit.email2"
                autocomplete="off"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Provincial Address:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-b-tooltip.hover
                title="Provincial address of the Employee"
                placeholder="Provincial address"
                v-model.trim="item_edit.provincial_address"
                autocomplete="off"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Provincial tel. #:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-b-tooltip.hover
                title="Provincial telephone number of the Employee"
                placeholder="Provincial telephone number"
                v-model.trim="item_edit.provincial_tel_no"
                autocomplete="off"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Birth Date:</p>
            </div>
            <div class="col-lg-9">
              <div class="input-group">
                <date-picker
                  v-model="item_edit.birth_date"
                  :config="Dateoptions"
                  v-b-tooltip.hover
                  title="Birth date of the employee"
                  placeholder="Birth date (yyyy-MM-dd)"
                  autocomplete="off"
                ></date-picker>
              </div>
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Birth Place:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-b-tooltip.hover
                title="Birth place of the Employee"
                placeholder="Birth place"
                v-model.trim="item_edit.birth_place"
                autocomplete="off"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Nationality:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-b-tooltip.hover
                title="Nationality of the Employee"
                placeholder="Nationality"
                v-model.trim="item_edit.nationality"
                autocomplete="off"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Religion:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-b-tooltip.hover
                title="Religion of the Employee"
                placeholder="Religion"
                v-model.trim="item_edit.religion"
                autocomplete="off"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">SSS no.:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-b-tooltip.hover
                title="SSS number of the Employee"
                placeholder="SSS number"
                v-model.trim="item_edit.sss_no"
                autocomplete="off"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Pag-ibig:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-b-tooltip.hover
                title="Pag-ibig of the Employee"
                placeholder="Pag-ibig"
                v-model.trim="item_edit.pag_ibig"
                autocomplete="off"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">PRC License:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-b-tooltip.hover
                title="PRC License of the Employee"
                placeholder="PRC License"
                v-model.trim="item_edit.prc_license"
                autocomplete="off"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">TIN No.:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-b-tooltip.hover
                title="TIN number of the Employee"
                placeholder="TIN number"
                v-model.trim="item_edit.tin_no"
                autocomplete="off"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Philhealth No.:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-b-tooltip.hover
                title="Philhealth number of the Employee"
                placeholder="Philhealth number"
                v-model.trim="item_edit.philhealth_no"
                autocomplete="off"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Height:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-b-tooltip.hover
                title="height of the Employee"
                placeholder="height"
                v-model.trim="item_edit.height"
                autocomplete="off"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Weight:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-b-tooltip.hover
                title="weight of the Employee"
                placeholder="weight"
                v-model.trim="item_edit.weight"
                autocomplete="off"
              />
            </div>
          </div>
        </div>
        <!-- /other_details -->

        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button
            size="sm"
            variant="success"
            v-if="item_edit.bioID == '' && roles.create_employee"
            @click="btnAdd()"
          >
            <i class="fas fa-plus-square"></i>
          </b-button>

          <b-button
            size="sm"
            variant="warning"
            v-b-modal="'ModalRole'"
            v-if="item_edit.bioID != '' && roles.role"
          >Role</b-button>

          <b-button
            size="sm"
            variant="warning"
            v-b-modal="'ModalResetPassword'"
            v-if="item_edit.bioID != '' && roles.update_employee"
          >Reset Password</b-button>

          <b-button
            size="sm"
            variant="success"
            v-if="item_edit.bioID != '' && roles.create_approver"
            @click="btnMakeApprover"
          >Make Approver</b-button>

          <b-button
            size="sm"
            variant="success"
            v-if="item_edit.bioID != '' && roles.update_employee"
            @click="btnUpdate"
          >Update</b-button>

          <b-button
            size="sm"
            variant="danger"
            v-if="item_edit.bioID != '' && roles.delete_employee"
            @click="btnDelete"
          >Delete</b-button>
        </template>
      </b-modal>
      <!-- End ModelAddEmployee -->

      <!--modalManageLeave ------------------------------------------------------------------------------------------->
      <b-modal
        id="modalManageLeave"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        title
        ok-only
      >
        <!--Form-------->
        <div class="elBG panel">
          <div class="panel-heading">
            <p class="elClr panel-title">
              Manage Leave for employee ID: {{ item_row_click.user.email }}
              <b-button
                v-b-modal="'ModalAddLeave'"
                type="button"
                class="btn btn-success btn-labeled pull-right margin-right-10"
              >
                <i class="fas fa-plus-square"></i>
              </b-button>
            </p>
          </div>
          <div class="elClr panel-body">
            <div>
              <b-row style="margin:10px;">
                <b-col md="5" class="my-1">
                  <b-form-group label-cols-sm="2" label="Filter" class="mb-0">
                    <b-input-group>
                      <b-form-input v-model="leave_tblFilter" placeholder="Filter"></b-form-input>
                      <b-input-group-append>
                        <b-button :disabled="!leave_tblFilter" @click="leave_tblFilter = ''">Clear</b-button>
                      </b-input-group-append>
                    </b-input-group>
                  </b-form-group>
                </b-col>
                <b-col md="5 " class="my-1"></b-col>

                <b-col md="2 " class="my-1">
                  <b-form-group label-cols-sm="4" label="Show" class="mb-0">
                    <b-form-select v-model="leave_perPage" :options="pageOptions"></b-form-select>
                  </b-form-group>
                </b-col>
              </b-row>

              <b-table
                class="elClr"
                striped
                show-empty
                hover
                outlined
                :fields="leave_fields"
                :items="leave_items"
                :filter="leave_tblFilter"
                :busy="leave_tblisBusy"
                :current-page="leave_currentPage"
                :per-page="leave_perPage"
                :tbody-tr-class="tblRowClass"
                head-variant=" elClr"
                thead-class="cursorPointer-th"
                @filtered="leave_onFiltered"
                @row-clicked="tblRowClicked_leave"
              >
                <div slot="table-busy" class="text-center text-danger my-2">
                  <b-spinner class="align-middle"></b-spinner>
                  <strong>Loading...</strong>
                </div>
              </b-table>
            </div>
          </div>
          <div class="elClr panel-footer">
            <div class="row" style="background-color:; padding:15px;">
              <div class="col-md-8" style="background-color:;">
                <span class="elClr">{{ leave_totalRows }} item/s found.</span>
              </div>

              <div class="col-md-4" style="background-color:;">
                <b-pagination
                  v-model="leave_currentPage"
                  :total-rows="leave_totalRows"
                  :per-page="leave_perPage"
                  class="my-0 pull-right"
                ></b-pagination>
              </div>
            </div>
          </div>
        </div>
        <!--Form-------->

        <template slot="modal-footer" slot-scope="{}"></template>
      </b-modal>
      <!--modalManageLeave-------->

      <!-- ModalAddLeave ---------------------------------------------------------------------------------------->
      <b-modal
        id="ModalAddLeave"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        ok-only
      >
        <template
          v-slot:modal-title
          v-if="item_selected.length > 0"
        >Add Leave Balance to all selected employee</template>
        <template v-else v-slot:modal-title>
          Add Leave Balance to employee ID :
          {{ item_row_click.user.email }} ({{ item_row_click.last_name }})
        </template>

        <!-- form -->

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Leave type:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="leave_types"
              v-model="leave_add.leave_type_id"
              option-value="id"
              option-text="name"
              placeholder="Select Leave type"
              name="leave_types"
              v-validate="'required'"
            ></model-list-select>
            <small
              class="text-danger pull-left"
              v-show="errors.has('leave_types')"
            >Leave type is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Year:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="year"
              v-validate="'required|between:2000,9000'"
              class="form-control"
              v-b-tooltip.hover
              title="Enroll Year"
              placeholder="Year"
              v-model.trim="leave_add.enroll_year"
              autocomplete="off"
            />
            <small class="text-danger pull-left" v-show="errors.has('year')">Input valid year.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Balance:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="balance"
              v-validate="'required|between:0,1000'"
              class="form-control"
              v-b-tooltip.hover
              title="Balance"
              placeholder="Balance"
              v-model.trim="leave_add.balance"
              autocomplete="off"
            />
            <small class="text-danger pull-left" v-show="errors.has('balance')">Input valid number.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Availed:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="availed"
              v-validate="'required|between:0,1000'"
              class="form-control"
              v-b-tooltip.hover
              title="Availed"
              placeholder="Availed"
              v-model.trim="leave_add.availed"
              autocomplete="off"
            />
            <small class="text-danger pull-left" v-show="errors.has('availed')">Input valid number.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Accrued:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="accrued"
              v-validate="'required|between:0,1000'"
              class="form-control"
              v-b-tooltip.hover
              title="Accrued"
              placeholder="Accrued"
              v-model.trim="leave_add.accrued"
              autocomplete="off"
            />
            <small class="text-danger pull-left" v-show="errors.has('accrued')">Input valid number.</small>
          </div>
        </div>
        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button
            size="sm"
            v-if="item_selected.length > 0"
            variant="success"
            @click="btnAddLeaveMultiple()"
          >
            <i class="fas fa-plus-square"></i>
          </b-button>
          <b-button size="sm" v-else variant="success" @click="btnAddLeave()">
            <i class="fas fa-plus-square"></i>
          </b-button>
        </template>
      </b-modal>
      <!-- End ModalAddLeave -->

      <!-- ModalEditLeave ---------------------------------------------------------------------------------------->
      <b-modal
        id="ModalEditLeave"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        ok-only
      >
        <template v-slot:modal-title>
          Update Leave Balance to employee ID :
          {{ item_row_click.user.email }} ({{ item_row_click.last_name }})
        </template>
        <!-- form -->

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Leave type:</p>
          </div>
          <div class="col-lg-9">
            <p class="textLabel">
              <b>{{ leave_edit.leave_type.name }}</b>
            </p>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Year:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="year"
              v-validate="'required|between:2000,9000'"
              class="form-control"
              v-b-tooltip.hover
              title="Enroll Year"
              placeholder="Year"
              v-model.trim="leave_edit.enroll_year"
              autocomplete="off"
            />
            <small class="text-danger pull-left" v-show="errors.has('year')">Input valid year.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Balance:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="balance"
              v-validate="'required|between:0,1000'"
              class="form-control"
              v-b-tooltip.hover
              title="Balance"
              placeholder="Balance"
              v-model.trim="leave_edit.balance"
              autocomplete="off"
            />
            <small class="text-danger pull-left" v-show="errors.has('balance')">Input valid number.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Availed:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="availed"
              v-validate="'required|between:0,1000'"
              class="form-control"
              v-b-tooltip.hover
              title="Availed"
              placeholder="Availed"
              v-model.trim="leave_edit.availed"
              autocomplete="off"
            />
            <small class="text-danger pull-left" v-show="errors.has('availed')">Input valid number.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Accrued:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="accrued"
              v-validate="'required|between:0,1000'"
              class="form-control"
              v-b-tooltip.hover
              title="Accrued"
              placeholder="Accrued"
              v-model.trim="leave_edit.accrued"
              autocomplete="off"
            />
            <small class="text-danger pull-left" v-show="errors.has('accrued')">Input valid number.</small>
          </div>
        </div>
        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnEditLeave()">Update</b-button>
        </template>
      </b-modal>
      <!-- End ModalEditLeave -->

      <!-- ModalSched ---------------------------------------------------------------------------------------->
      <b-modal
        id="ModalSched"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        ok-only
      >
        <template v-slot:modal-title>
          DTR of employee ID : {{ item_row_click.user.email }} ({{
          item_row_click.last_name
          }})
        </template>

        <!-- form -->
        <div class="elBG panel">
          <div class="panel-heading">
            <div class="rowFields mx-auto row">
              <div class="col-lg-2">
                <p class="elClr panel-title" style="margin-top:9px;">Period:</p>
              </div>
              <div class="col-lg-6">
                <model-list-select
                  :list="pay_period_list"
                  v-model="pay_period_select"
                  option-value="id"
                  option-text="period"
                  placeholder="Select pay period"
                  name="pay_period_list"
                  @input="pay_period_onchange"
                  v-validate="'required'"
                ></model-list-select>
              </div>
              <div class="col-lg-2"></div>
              <div class="col-lg-2">
                <b-button
                  v-b-modal="'ModalAddSched'"
                  type="button"
                  class="btn btn-success btn-labeled pull-right margin-right-10"
                  v-if="roles.create_dtr"
                >
                  <i class="fas fa-plus-square"></i>
                </b-button>
              </div>
            </div>
          </div>
          <div class="elClr panel-body">
            <div>
              <b-row style="margin:10px;">
                <b-col md="5" class="my-1">
                  <b-form-group label-cols-sm="2" label="Filter" class="mb-0">
                    <b-input-group>
                      <b-form-input v-model="sched_tblFilter" placeholder="Filter"></b-form-input>
                      <b-input-group-append>
                        <b-button :disabled="!sched_tblFilter" @click="sched_tblFilter = ''">Clear</b-button>
                      </b-input-group-append>
                    </b-input-group>
                  </b-form-group>
                </b-col>
                <b-col md="5 " class="my-1"></b-col>

                <b-col md="2 " class="my-1">
                  <b-form-group label-cols-sm="4" label="Show" class="mb-0">
                    <b-form-select v-model="sched_perPage" :options="pageOptions"></b-form-select>
                  </b-form-group>
                </b-col>
              </b-row>

              <b-table
                class="elClr"
                striped
                show-empty
                hover
                outlined
                :fields="sched_fields"
                :items="sched_items"
                :filter="sched_tblFilter"
                :busy="sched_tblisBusy"
                :current-page="sched_currentPage"
                :per-page="sched_perPage"
                :tbody-tr-class="tblRowClass"
                head-variant=" elClr"
                thead-class="cursorPointer-th"
                @filtered="sched_onFiltered"
              >
                <div slot="table-busy" class="text-center text-danger my-2">
                  <b-spinner class="align-middle"></b-spinner>
                  <strong>Loading...</strong>
                </div>
                <template v-slot:cell(shift_sched)="data">
                  <span v-html="data.value"></span>
                </template>
              </b-table>
            </div>
          </div>
          <div class="elClr panel-footer">
            <div class="row" style="background-color:; padding:15px;">
              <div class="col-md-8" style="background-color:;">
                <span class="elClr">{{ sched_totalRows }} item/s found.</span>
              </div>

              <div class="col-md-4" style="background-color:;">
                <b-pagination
                  v-model="sched_currentPage"
                  :total-rows="sched_totalRows"
                  :per-page="sched_perPage"
                  class="my-0 pull-right"
                ></b-pagination>
              </div>
            </div>
          </div>
        </div>
        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}"></template>
      </b-modal>
      <!-- End ModalSched -->

      <!-- ModalAddSched ---------------------------------------------------------------------------------------->
      <b-modal
        id="ModalAddSched"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        ok-only
      >
        <template
          v-slot:modal-title
          v-if="item_selected.length > 0"
        >Add Schedule to all selected employee</template>
        <template v-slot:modal-title v-else>
          Add Schedule to employee ID : {{ item_row_click.user.email }} ({{
          item_row_click.last_name
          }})
        </template>
        <!-- form -->

        <div class="rowFields mx-auto row">
          <div class="col-lg-2">
            <p class="textLabel">Period:</p>
          </div>
          <div class="col-lg-6">
            <model-list-select
              :list="pay_period_list"
              v-model="pay_period_select_add"
              option-value="id"
              option-text="period"
              placeholder="Select pay period"
              name="pay_period_list"
              @input="pay_period_onchange_add"
              v-validate="'required'"
            ></model-list-select>
          </div>
          <div class="col-lg-2"></div>
          <div class="col-lg-2"></div>
        </div>

        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">
                <b>Work Date</b>
              </p>
            </div>
            <div class="col-lg-1">
              <p class="textLabel">
                <b>Rest day</b>
              </p>
            </div>
            <div class="col-lg-2">
              <p class="textLabel">
                <b></b>
              </p>
            </div>
            <div class="col-lg-3">
              <p class="textLabel">
                <b>Shift sched. in</b>
              </p>
            </div>
            <div class="col-lg-3">
              <p class="textLabel">
                <b>Shift sched. out</b>
              </p>
            </div>
          </div>

          <div
            class="rowFields mx-auto row"
            v-for="sched_item in sched_items_add"
            :key="sched_item.work_date"
          >
            <div class="col-lg-3">
              <p class="textLabel">{{ sched_item.work_date }} ({{ sched_item.day }})</p>
            </div>
            <div class="col-lg-1">
              <p-check
                class="textLabel p-switch p-fill"
                color="success"
                v-model="sched_item.is_rest_day"
              ></p-check>
            </div>
            <div class="col-lg-2"></div>
            <div class="col-lg-3">
              <date-picker
                v-if="!sched_item.is_rest_day"
                name="date"
                v-model="sched_item.shift_sched_in"
                :config="DateTimeOptions"
              ></date-picker>
              <p class="textLabel" v-else>
                <center>
                  <b>-</b>
                </center>
              </p>
            </div>
            <div class="col-lg-3">
              <date-picker
                v-if="!sched_item.is_rest_day"
                name="date"
                v-model="sched_item.shift_sched_out"
                :config="DateTimeOptions"
              ></date-picker>
              <p class="textLabel" v-else>
                <center>
                  <b>-</b>
                </center>
              </p>
            </div>
          </div>
        </div>
        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button
            size="sm"
            v-if="item_selected.length > 0"
            variant="success"
            @click="btnAddSchedMultiple()"
          >
            <i class="fas fa-plus-square"></i>
          </b-button>

          <b-button size="sm" v-else variant="success" @click="btnAddSched()">
            <i class="fas fa-plus-square"></i>
          </b-button>
        </template>
      </b-modal>
      <!-- End ModalAddSched -->

      <!--modalManageApprover ------------------------------------------------------------------------------------------->
      <b-modal
        id="modalManageApprover"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        title
        ok-only
      >
        <!--Form-------->
        <div class="elBG panel">
          <div class="panel-heading">
            <p class="elClr panel-title">
              Manage Approver for employee ID: {{ item_row_click.user.email }}
              <b-button
                v-b-modal="'ModalAddApprover'"
                type="button"
                class="btn btn-success btn-labeled pull-right margin-right-10"
                v-if="roles.create_approver"
              >Add Approver</b-button>
            </p>
          </div>
          <div class="elClr panel-body">
            <div>
              <b-row style="margin:10px;">
                <b-col md="5" class="my-1">
                  <b-form-group label-cols-sm="2" label="Filter" class="mb-0">
                    <b-input-group>
                      <b-form-input v-model="approve_tblFilter" placeholder="Filter"></b-form-input>
                      <b-input-group-append>
                        <b-button
                          :disabled="!approve_tblFilter"
                          @click="approve_tblFilter = ''"
                        >Clear</b-button>
                      </b-input-group-append>
                    </b-input-group>
                  </b-form-group>
                </b-col>
                <b-col md="5 " class="my-1"></b-col>

                <b-col md="2 " class="my-1">
                  <b-form-group label-cols-sm="4" label="Show" class="mb-0">
                    <b-form-select v-model="approve_perPage" :options="pageOptions"></b-form-select>
                  </b-form-group>
                </b-col>
              </b-row>

              <b-table
                class="elClr"
                striped
                show-empty
                hover
                outlined
                :fields="approve_fields"
                :items="approve_items"
                :filter="approve_tblFilter"
                :busy="approve_tblisBusy"
                :current-page="approve_currentPage"
                :per-page="approve_perPage"
                :tbody-tr-class="tblRowClass"
                head-variant=" elClr"
                thead-class="cursorPointer-th"
                @filtered="approve_onFiltered"
              >
                <div slot="table-busy" class="text-center text-danger my-2">
                  <b-spinner class="align-middle"></b-spinner>
                  <strong>Loading...</strong>
                </div>
              </b-table>
            </div>
          </div>
          <div class="elClr panel-footer">
            <div class="row" style="background-color:; padding:15px;">
              <div class="col-md-8" style="background-color:;">
                <span class="elClr">{{ approve_totalRows }} item/s found.</span>
              </div>

              <div class="col-md-4" style="background-color:;">
                <b-pagination
                  v-model="approve_currentPage"
                  :total-rows="approve_totalRows"
                  :per-page="approve_perPage"
                  class="my-0 pull-right"
                ></b-pagination>
              </div>
            </div>
          </div>
        </div>
        <!--Form-------->

        <template slot="modal-footer" slot-scope="{}"></template>
      </b-modal>
      <!--modalManageApprover-------->

      <!-- ModalAddApprover ---------------------------------------------------------------------------------------->
      <b-modal
        id="ModalAddApprover"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        ok-only
      >
        <template v-slot:modal-title>
          Add Approver to employee ID : {{ item_row_click.user.email }} ({{
          item_row_click.last_name
          }})
        </template>
        <!-- form -->

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Approver name:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="approvers"
              v-model="approve_add.approver_id"
              option-value="id"
              :custom-text="customeApproverName"
              placeholder="Select Approver name"
              name="approver"
              v-validate="'required'"
            ></model-list-select>
            <small
              class="text-danger pull-left"
              v-show="errors.has('approver')"
            >Approver name is required.</small>
          </div>
        </div>
        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnAddApproverOK()">Add</b-button>
        </template>
      </b-modal>
      <!-- End ModalAddApprover -->

      <!-- ModalManagePayslip ---------------------------------------------------------------------------------------->
      <b-modal
        id="ModalManagePayslip1"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="lg"
        ok-only
      >
        <template v-slot:modal-title>
          Manage payslip for employee ID : {{ item_row_click.user.email }} ({{
          item_row_click.last_name
          }})
        </template>
        <!-- form -->

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Period:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="pay_period_list"
              v-model="pay_period_select"
              option-value="id"
              option-text="period"
              placeholder="Select pay period"
              name="pay_period_list"
              @input="pay_period_onchange"
              v-validate="'required'"
            ></model-list-select>
          </div>
        </div>
        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">13th Month Pay:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                v-b-tooltip.hover
                placeholder="13th Month Pay"
                v-model.trim="payslip.month_pay"
              />
            </div>
          </div>
        </div>
        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Tax Refund:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                v-b-tooltip.hover
                placeholder="Tax Refund"
                v-model.trim="payslip.tax_refund"
              />
            </div>
          </div>
        </div>
        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">VL Conversion:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                v-b-tooltip.hover
                placeholder="VL Conversion"
                v-model.trim="payslip.vl_conversion"
              />
            </div>
          </div>
        </div>
        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Incentive:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                v-b-tooltip.hover
                placeholder="Incentive"
                v-model.trim="payslip.incentive"
              />
            </div>
          </div>
        </div>
        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Other Bunoses:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                v-b-tooltip.hover
                placeholder="Other Bunoses"
                v-model.trim="payslip.other_bunos"
              />
            </div>
          </div>
        </div>
        <hr />
        <span>
          <b>DEDUCTIONS</b>
        </span>
        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Uniform:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                v-b-tooltip.hover
                placeholder="Uniform"
                v-model.trim="payslip.Uniform"
              />
            </div>
          </div>
        </div>
        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">SSS:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                v-b-tooltip.hover
                placeholder="SSS"
                v-model.trim="payslip.sss"
              />
            </div>
          </div>
        </div>
        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">PHIC:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                v-b-tooltip.hover
                placeholder="PHIC"
                v-model.trim="payslip.phic"
              />
            </div>
          </div>
        </div>
        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">HDMF:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                v-b-tooltip.hover
                placeholder="HDMF"
                v-model.trim="payslip.hdmf"
              />
            </div>
          </div>
        </div>
        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">WTX (tax due):</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                v-b-tooltip.hover
                placeholder="WTX (tax due)"
                v-model.trim="payslip.wtx"
              />
            </div>
          </div>
        </div>
        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">SSS Loans:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                v-b-tooltip.hover
                placeholder="SSS Loans"
                v-model.trim="payslip.sss_loan"
              />
            </div>
          </div>
        </div>
        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">HDMF Loan:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                v-b-tooltip.hover
                placeholder="HDMF Loan"
                v-model.trim="payslip.hdmf_loan"
              />
            </div>
          </div>
        </div>
        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Cellphone Charges:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                v-b-tooltip.hover
                placeholder="Cellphone Charges"
                v-model.trim="payslip.cp_charge"
              />
            </div>
          </div>
        </div>
        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Cash Advances:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                v-b-tooltip.hover
                placeholder="Cash Advance"
                v-model.trim="payslip.cash_advance"
              />
            </div>
          </div>
        </div>
        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Others:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                v-b-tooltip.hover
                placeholder="Other Deductions"
                v-model.trim="payslip.other_deduction"
              />
            </div>
          </div>
        </div>
        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">TOTAL DEDUCTIONS:</p>
            </div>
            <div class="col-lg-9">
              <p class="textLabel">
                <b>00000</b>
              </p>
            </div>
          </div>
        </div>

        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">NET PAY:</p>
            </div>
            <div class="col-lg-9">
              <p class="textLabel">
                <b>00000</b>
              </p>
            </div>
          </div>
        </div>
        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnAddApproverOK()">Add</b-button>
        </template>
      </b-modal>
      <!-- End ModalManagePayslip -->

      <!-- ModalResetPassword ---------------------------------------------------------------------------------------->
      <b-modal
        id="ModalResetPassword"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="lg"
        ok-only
      >
        <template v-slot:modal-title>
          Reset Password for employee ID : {{ item_edit.user.email }} ({{
          item_edit.last_name
          }})
        </template>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">New password:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="password"
              class="form-control"
              v-b-tooltip.hover
              placeholder="New password"
              v-model.trim="item_edit.password"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Re-type new password:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="password"
              class="form-control"
              v-b-tooltip.hover
              placeholder="re-type new password"
              v-model.trim="item_edit.repassword"
            />
          </div>
        </div>

        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnSubmitResetPassword()">Update</b-button>
          <b-button size="sm" variant="danger" @click="$bvModal.hide('ModalResetPassword')">Cancel</b-button>
        </template>
      </b-modal>
      <!-- End ModalResetPassword -->

      <!-- ModalRole ---------------------------------------------------------------------------------------->
      <b-modal
        id="ModalRole"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        ok-only
      >
        <template v-slot:modal-title>
          Manage Role for employee ID : {{ item_edit.user.email }} ({{
          item_edit.last_name
          }})
        </template>

        <!-- Leave -->
        <div class="rowFields1 row role-container" style>
          <div class="col-lg-3">
            <b>Leave:</b>
          </div>

          <div class="col-lg-2">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_leave"
            ></p-check>
          </div>
          <div class="col-lg-2">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_leave"
            ></p-check>
          </div>

          <div class="col-lg-2">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_leave"
            ></p-check>
          </div>

          <div class="col-lg-2">
            View:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.view_leave"
            ></p-check>
          </div>
        </div>

        <hr />
        <!-- DTR -->
        <div class="rowFields1 row role-container" style>
          <div class="col-lg-3">
            <b>DTR:</b>
          </div>

          <div class="col-lg-2">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_dtr"
            ></p-check>
          </div>
          <div class="col-lg-2">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_dtr"
            ></p-check>
          </div>

          <div class="col-lg-2">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_dtr"
            ></p-check>
          </div>

          <div class="col-lg-2">
            View:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.view_dtr"
            ></p-check>
          </div>
        </div>
        <hr />
        <!-- Approver -->
        <div class="rowFields1 row role-container" style>
          <div class="col-lg-3">
            <b>Approver:</b>
          </div>

          <div class="col-lg-2">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_approver"
            ></p-check>
          </div>
          <div class="col-lg-2">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_approver"
            ></p-check>
          </div>

          <div class="col-lg-2">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_approver"
            ></p-check>
          </div>

          <div class="col-lg-2">
            View:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.view_approver"
            ></p-check>
          </div>
        </div>
        <hr />
        <!-- payslip -->
        <div class="rowFields1 row role-container" style>
          <div class="col-lg-3">
            <b>Payslip:</b>
          </div>

          <div class="col-lg-2">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_payslip"
            ></p-check>
          </div>
          <div class="col-lg-2">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_payslip"
            ></p-check>
          </div>

          <div class="col-lg-2">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_payslip"
            ></p-check>
          </div>

          <div class="col-lg-2">
            View:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.view_payslip"
            ></p-check>
          </div>
        </div>
        <hr />
        <!-- employee -->
        <div class="rowFields1 row role-container" style>
          <div class="col-lg-3">
            <b>Employee:</b>
          </div>

          <div class="col-lg-2">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_employee"
            ></p-check>
          </div>

          <div class="col-lg-2">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_employee"
            ></p-check>
          </div>
          <div class="col-lg-2">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_employee"
            ></p-check>
          </div>

          <div class="col-lg-2"></div>
        </div>
        <hr />
        <!-- group -->
        <div class="rowFields1 row role-container" style>
          <div class="col-lg-3">
            <b>Group:</b>
          </div>

          <div class="col-lg-2">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_group"
            ></p-check>
          </div>

          <div class="col-lg-2">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_group"
            ></p-check>
          </div>
          <div class="col-lg-2">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_group"
            ></p-check>
          </div>

          <div class="col-lg-2"></div>
        </div>
        <hr />
        <!-- position -->
        <div class="rowFields1 row role-container" style>
          <div class="col-lg-3">
            <b>Position:</b>
          </div>

          <div class="col-lg-2">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_position"
            ></p-check>
          </div>

          <div class="col-lg-2">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_position"
            ></p-check>
          </div>
          <div class="col-lg-2">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_position"
            ></p-check>
          </div>

          <div class="col-lg-2"></div>
        </div>
        <hr />
        <!-- department -->
        <div class="rowFields1 row role-container" style>
          <div class="col-lg-3">
            <b>Department:</b>
          </div>

          <div class="col-lg-2">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_department"
            ></p-check>
          </div>

          <div class="col-lg-2">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_department"
            ></p-check>
          </div>
          <div class="col-lg-2">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_department"
            ></p-check>
          </div>

          <div class="col-lg-2"></div>
        </div>
        <hr />
        <!-- pay period -->
        <div class="rowFields1 row role-container" style>
          <div class="col-lg-3">
            <b>Pay period:</b>
          </div>

          <div class="col-lg-2">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_pay_period"
            ></p-check>
          </div>

          <div class="col-lg-2">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_pay_period"
            ></p-check>
          </div>
          <div class="col-lg-2">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_pay_period"
            ></p-check>
          </div>

          <div class="col-lg-2"></div>
        </div>
        <hr />
        <!-- Rate -->
        <div class="rowFields1 row role-container" style>
          <div class="col-lg-3">
            <b>Rate:</b>
          </div>

          <div class="col-lg-2">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_rate"
            ></p-check>
          </div>

          <div class="col-lg-2">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_rate"
            ></p-check>
          </div>
          <div class="col-lg-2">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_rate"
            ></p-check>
          </div>

          <div class="col-lg-2"></div>
        </div>
        <hr />
        <!-- Branch -->
        <div class="rowFields1 row role-container" style>
          <div class="col-lg-3">
            <b>Branch:</b>
          </div>

          <div class="col-lg-2">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_branch"
            ></p-check>
          </div>

          <div class="col-lg-2">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_branch"
            ></p-check>
          </div>
          <div class="col-lg-2">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_branch"
            ></p-check>
          </div>

          <div class="col-lg-2"></div>
        </div>
        <hr />
        <!-- Calendar -->
        <div class="rowFields1 row role-container" style>
          <div class="col-lg-3">
            <b>Calendar:</b>
          </div>

          <div class="col-lg-2">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_calendar"
            ></p-check>
          </div>

          <div class="col-lg-2">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_calendar"
            ></p-check>
          </div>
          <div class="col-lg-2">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_calendar"
            ></p-check>
          </div>

          <div class="col-lg-2"></div>
        </div>
        <hr />
        <!-- update_RAlog -->
        <div class="rowFields1 row role-container" style>
          <div class="col-lg-3">
            <b>update_RAlog:</b>
          </div>

          <div class="col-lg-2">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_RAlog"
            ></p-check>
          </div>
        </div>
        <!-- manage_leave -->
        <div class="rowFields1 row role-container" style>
          <div class="col-lg-3">
            <b>manage_leave:</b>
          </div>

          <div class="col-lg-2">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.manage_leave"
            ></p-check>
          </div>
        </div>
        <!-- Operator -->
        <div class="rowFields1 row role-container" style>
          <div class="col-lg-3">
            <b>Operator:</b>
          </div>

          <div class="col-lg-2">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.operator"
            ></p-check>
          </div>
        </div>
        <!-- HR -->
        <div class="rowFields1 row role-container" style>
          <div class="col-lg-3">
            <b>HR:</b>
          </div>

          <div class="col-lg-2">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.hr"
            ></p-check>
          </div>
        </div>
        <!-- Employee -->
        <div class="rowFields1 row role-container" style="display: none;">
          <div class="col-lg-3">
            <b>Employee:</b>
          </div>

          <div class="col-lg-2">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.employee"
            ></p-check>
          </div>
        </div>
        <!-- Admin -->
        <div class="rowFields1 row role-container" style>
          <div class="col-lg-3">
            <b>Admin:</b>
          </div>

          <div class="col-lg-2">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.admin"
            ></p-check>
          </div>
        </div>
        <!-- RM -->
        <div class="rowFields1 row role-container" style>
          <div class="col-lg-3">
            <b>RM:</b>
          </div>

          <div class="col-lg-2">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.rm"
            ></p-check>
          </div>
        </div>
        <!-- Network -->
        <div class="rowFields1 row role-container" style>
          <div class="col-lg-3">
            <b>Network:</b>
          </div>

          <div class="col-lg-2">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.network"
            ></p-check>
          </div>
        </div>

        <!-- earnings -->
        <div class="rowFields1 row role-container" style>
          <div class="col-lg-3">
            <b>Earning:</b>
          </div>

          <div class="col-lg-2">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.earnings"
            ></p-check>
          </div>
        </div>

        <!-- deduction -->
        <div class="rowFields1 row role-container" style>
          <div class="col-lg-3">
            <b>Deduction:</b>
          </div>

          <div class="col-lg-2">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.deduction"
            ></p-check>
          </div>
        </div>
        <!-- Role -->
        <div class="rowFields1 row role-container" style>
          <div class="col-lg-3">
            <b>Role:</b>
          </div>

          <div class="col-lg-2">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.role"
            ></p-check>
          </div>
        </div>

        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnUpdateRoles()">Update</b-button>
        </template>
      </b-modal>
      <!-- End ModalRole -->
    </div>

    <!-- other modals -->
    <earning v-bind:data="item_row_click"></earning>
    <deduction v-bind:data="item_row_click"></deduction>
    <payslip v-bind:data="item_row_click"></payslip>
    <modal_emp_filter></modal_emp_filter>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";
import PrettyCheck from "pretty-checkbox-vue/check";

import "bootstrap/dist/css/bootstrap.css";
import datePicker from "vue-bootstrap-datetimepicker";
import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";
import hrReport from "./../../modal_vue/modal_hr_report.vue";

import earning from "../../modal_vue/modal_earnings.vue";
import deduction from "../../modal_vue/modal_deduction.vue";
import payslip from "../../modal_vue/modal_payslip.vue";
import modal_emp_filter from "../../modal_vue/modal_emp_filter.vue";

export default {
  components: {
    modal_emp_filter: modal_emp_filter,
    hrReport: hrReport,
    payslip: payslip,
    earning: earning,
    deduction: deduction,
    "date-picker": datePicker,
    "p-check": PrettyCheck,
    "model-list-select": ModelListSelect
  },
  data() {
    return {
      view_other_details: false,
      tblisBusy: true,
      fields: [
        { key: "chkbox", label: "", sortable: true },
        { key: "user.email", label: "ID", sortable: true },
        {
          key: "Name",
          label: "Full Name",
          formatter: (value, key, item) => {
            if (item.middle_name == null) item.middle_name = "";
            return (
              item.first_name + " " + item.middle_name + " " + item.last_name
            );
          },
          sortable: true
        },
        { key: "group.name", label: "Group", sortable: true },
        { key: "position.name", label: "Position", sortable: true },
        { key: "branch.name", label: "Branch", sortable: true },
        { key: "department.name", label: "Department", sortable: true },
        { key: "actions", sortable: true }
      ],
      items: [],
      item_selected: [],
      tblFilter: null,
      totalRows: 1,
      currentPage: 2,
      perPage: 10,
      pageOptions: [10, 25, 50, 100, 1000],
      item_add: {},
      item_edit: {
        bioID: "",
        id: null,
        group_id: "",
        rate_id: "",
        position_id: "",
        branch_id: "",
        department_id: "",
        employment_status: "",
        first_name: "",
        middle_name: "",
        last_name: "",
        gender: "",
        date_hired: "",
        permanent_address: "",
        tel_no: "",
        mobile_no: "",
        email1: "",
        email2: "",
        provincial_address: "",
        provincial_tel_no: "",
        birth_date: "",
        birth_place: "",
        nationality: "",
        religion: "",
        sss_no: "",
        pag_ibig: "",
        prc_license: "",
        civil_status: "",
        height: "",
        weight: "",
        tin_no: "",
        philhealth_no: "",
        password: "",
        repassword: ""
      },
      genders: [
        {
          name: "Male"
        },
        {
          name: "Female"
        }
      ],
      employment_statuses: [
        {
          name: "Trainee"
        },
        {
          name: "Provisionary"
        },
        {
          name: "Regular"
        },
        {
          name: "Resigned"
        },
        {
          name: "Terminated"
        }
      ],
      Dateoptions: {
        format: "YYYY-MM-DD",
        useCurrent: false
      },
      DateTimeOptions: {
        // https://momentjs.com/docs/#/displaying/ YYYY-MM-DD h:mm
        format: "YYYY-MM-DD HH:mm",
        useCurrent: false,
        showClear: true,
        showClose: true
      },
      groups: [],
      departments: [],
      branches: [],
      positions: [],
      rates: [],

      item_row_click: {
        user: {
          email: ""
        }
      },
      leave_tblisBusy: true,
      leave_fields: [
        { key: "leave_type.name", label: "Leave type", sortable: true },
        { key: "enroll_year", sortable: true },
        { key: "balance", sortable: true },
        { key: "availed", sortable: true },
        { key: "accrued", sortable: true }
      ],
      leave_items: [],
      leave_tblFilter: null,
      leave_totalRows: 0,
      leave_currentPage: 1,
      leave_perPage: 5,
      leave_add: {
        employee_id: "",
        leave_type_id: "",
        enroll_year: "",
        balance: "0",
        availed: "0",
        accrued: "0"
      },
      leave_edit: {
        leave_type: {
          name: ""
        },
        employee_id: "",
        leave_type_id: "",
        enroll_year: "",
        balance: "0",
        availed: "0",
        accrued: "0"
      },
      leave_types: [],
      pay_period_list: [],
      pay_period_select: "",
      pay_period_select_add: {},
      sched_tblisBusy: false,
      sched_fields: [
        { key: "work_date", sortable: true },
        { key: "day", sortable: true },
        {
          key: "shift_sched",
          label: "Shift Sched",
          formatter: (value, key, item) => {
            if (item.is_rest_day == 1)
              return "<p class='text-danger'>Rest Day</p>";
            else if (item.is_rest_day == 2)
              return "<p class='text-danger'>Leave</p>";
            else if (item.is_rest_day == 3)
              return "<p class='text-danger'>Holiday</p>";
            else return item.shift_sched_in + " - " + item.shift_sched_out;
          },
          sortable: true
        },
        { key: "time_in", sortable: true },
        { key: "time_out", sortable: true },
        { key: "summary.late", label: "Late", sortable: true },
        { key: "summary.undertime", label: "Undertime", sortable: true }
      ],
      sched_items: [],
      sched_tblFilter: null,
      sched_totalRows: 0,
      sched_currentPage: 1,
      sched_perPage: 20,
      sched_items_add: [],

      approve_tblisBusy: true,
      approve_fields: [
        {
          key: "appname",
          label: "Approver name",
          formatter: (value, key, item) => {
            return (
              item.app.employee.first_name + " " + item.app.employee.last_name
            );
          }
        },
        { key: "level", sortable: true }
      ],
      approve_items: [],
      approve_tblFilter: null,
      approve_totalRows: 0,
      approve_currentPage: 1,
      approve_perPage: 5,
      approve_add: {
        approver_id: "",
        employee_id: ""
      },
      approvers: [],
      payslip: {
        month_pay: "",
        tax_refund: "",
        vl_conversion: "",
        incentive: "",
        other_bunos: "",
        uniform: "",
        sss: "",
        phic: "",
        hdmf: "",
        wtx: "",
        sss_loan: "",
        hdmf_loan: "",
        cp_charge: "",
        cash_advance: "",
        other_deduction: ""
      },
      checked_count: 0,
      editRoles: {
        id: "",
        roles: {
          create_employee: false,
          update_employee: false,
          delete_employee: false,
          update_RAlog: false,
          view_leave: false,
          create_leave: false,
          update_leave: false,
          delete_leave: false,
          view_dtr: false,
          create_dtr: false,
          update_dtr: false,
          delete_dtr: false,
          view_approver: false,
          create_approver: false,
          update_approver: false,
          delete_approver: false,

          view_payslip: false,
          create_payslip: false,
          update_payslip: false,
          delete_payslip: false,

          create_group: false,
          update_group: false,
          delete_group: false,

          create_position: false,
          update_position: false,
          delete_position: false,

          create_department: false,
          update_department: false,
          delete_department: false,

          create_pay_period: false,
          update_pay_period: false,
          delete_pay_period: false,

          create_rate: false,
          update_rate: false,
          delete_rate: false,

          create_branch: false,
          update_branch: false,
          delete_branch: false,

          create_calendar: false,
          update_calendar: false,
          delete_calendar: false,

          manage_leave: false,
          earnings: false,
          deduction: false
        }
      },
      user: {},
      roles: []
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.user = this.$global.getUser();
    this.roles = this.$global.getRoles();
    this.groups = this.$global.getGroup();
    this.positions = this.$global.getPosition();
    this.branches = this.$global.getBranch();
    this.departments = this.$global.getDepartment();
    this.leave_types = this.$global.getLeaveType();
    this.load_items("Employee");
    this.load_rates();
    this.load_pay_period();
  },
  mounted() {
    //this.totalRows = this.items.length;

    this.$root.$on("update_item", item => {
      this.items = item;
      this.totalRows = this.items.length;
    });
  },
  updated() {},
  methods: {
    load_items(model) {
      this.$http.get("api/" + model).then(function(response) {
        this.items = response.body;
        this.totalRows = this.items.length;
        this.tblisBusy = false;
      });
    },
    load_rates() {
      this.$http.get("api/Rate").then(function(response) {
        this.rates = response.body;
      });
    },
    load_pay_period() {
      this.$http.get("api/PayPeriod").then(function(response) {
        this.pay_period_list = response.body;
      });
    },
    tblRowClass(item, type) {
      if (!item) return;
      else return "elClr cursorPointer";
    },
    tblHeadClass(item, type) {
      if (!item) return;
      else {
        return "elClr";
      }
    },
    openModalEmployee() {
      this.$bvModal.show("ModelAddEmployee");
      if (this.item_edit.bioID != "")
        this.item_edit = {
          bioID: "",
          id: "",
          group_id: "",
          rate_id: "",
          position_id: "",
          branch_id: "",
          department_id: "",
          employment_status: "",
          first_name: "",
          middle_name: "",
          last_name: "",
          gender: "",
          date_hired: "",
          permanent_address: "",
          tel_no: "",
          mobile_no: "",
          email1: "",
          email2: "",
          provincial_address: "",
          provincial_tel_no: "",
          birth_date: "",
          birth_place: "",
          nationality: "",
          religion: "",
          sss_no: "",
          pag_ibig: "",
          prc_license: "",
          civil_status: "",
          height: "",
          weight: "",
          tin_no: "",
          philhealth_no: ""
        };
    },
    onFiltered(filteredItems) {
      this.totalRows = filteredItems.length;
      this.currentPage = 1;
    },
    leave_onFiltered(filteredItems) {
      this.leave_totalRows = filteredItems.length;
      this.leave_currentPage = 1;
    },
    sched_onFiltered(filteredItems) {
      this.sched_totalRows = filteredItems.length;
      this.sched_currentPage = 1;
    },
    approve_onFiltered(filteredItems) {
      this.approve_totalRows = filteredItems.length;
      this.approve_currentPage = 1;
    },
    tblRowClicked(item, index, event) {
      this.$bvModal.show("ModelAddEmployee");
      this.item_edit = item;
      this.item_edit.bioID = item.id;
      console.log(item);

      this.editRoles.id = item.user.id;
      this.editRoles.roles = item.roleList;
      this.editRoles.roless = item.roles;
    },
    tblRowClicked_leave(item, index, event) {
      this.$bvModal.show("ModalEditLeave");
      this.leave_edit = item;
      console.log(this.leave_edit);
    },
    handleOk(bvModalEvt) {
      //bvModalEvt.preventDefault();
    },
    btnAdd() {
      console.log(this.item_edit);

      this.$validator.validateAll().then(result => {
        if (result) {
          this.$root.$emit("pageLoading");
          this.item_edit.user_id = this.user.id;
          this.item_edit.user_name = this.user.email;
          this.$http
            .post("api/Employee", this.item_edit)
            .then(response => {
              this.$root.$emit("pageLoaded");
              console.log(response.body);
              swal("Notification", "Added successfully", "success");

              this.$bvModal.hide("ModelAddEmployee");
              this.items = response.body;
              this.totalRows = this.items.length;
              this.item_edit = {
                bioID: "",
                id: "",
                group_id: "",
                rate_id: "",
                position_id: "",
                branch_id: "",
                department_id: "",
                employment_status: "",
                first_name: "",
                middle_name: "",
                last_name: "",
                gender: "",
                date_hired: "",
                permanent_address: "",
                tel_no: "",
                mobile_no: "",
                email1: "",
                email2: "",
                provincial_address: "",
                provincial_tel_no: "",
                birth_date: "",
                birth_place: "",
                nationality: "",
                religion: "",
                sss_no: "",
                pag_ibig: "",
                prc_license: "",
                civil_status: "",
                height: "",
                weight: "",
                tin_no: "",
                philhealth_no: ""
              };
            })
            .catch(response => {
              this.$root.$emit("pageLoaded");
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              });
            });
        }
      });
    },
    btnUpdate() {
      this.$validator.validateAll().then(result => {
        if (result) {
          swal({
            title: "Are you sure?",
            text: "Do you want to Update this?",
            icon: "warning",
            buttons: true,
            dangerMode: true
          }).then(update => {
            this.tblisBusy = true;
            if (update) {
              this.$root.$emit("pageLoading");
              this.item_edit.user_id = this.user.id;
              this.item_edit.user_name = this.user.email;
              this.$http
                .put("api/Employee/" + this.item_edit.bioID, this.item_edit)
                .then(response => {
                  this.$root.$emit("pageLoaded");
                  this.items = response.body;
                  this.totalRows = this.items.length;
                  swal("Update!", "Update successfully", "success");
                  this.$bvModal.hide("ModelAddEmployee");
                  this.tblisBusy = false;
                })
                .catch(response => {
                  this.$root.$emit("pageLoaded");
                  swal({
                    title: "Error",
                    text: response.body.error,
                    icon: "error",
                    dangerMode: true
                  });
                });
            }
          });
        }
      });
    },
    btnDelete() {
      swal({
        title: "Are you sure?",
        text: "Do you really want to delete this item permanently",
        icon: "warning",
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.$root.$emit("pageLoading");
          this.items = [];
          this.tblisBusy = true;
          this.$http
            .delete("api/Employee/" + this.item_edit.bioID)
            .then(response => {
              this.$root.$emit("pageLoaded");
              this.$bvModal.hide("modalEdit");
              swal("Deleted!", "Item has been deleted", "success").then(
                value => {
                  this.items = response.body;
                  this.totalRows = this.items.length;
                  this.tblisBusy = false;
                }
              );
            })
            .catch(response => {
              this.$root.$emit("pageLoaded");
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              });
            });
        }
      });
    },
    // LEAVESS functions
    openModalLeave(item) {
      this.item_row_click = item;
      this.uncheckAllSelectedEmp();
      this.$bvModal.show("modalManageLeave");
      this.load_leave_balance(this.item_row_click.id);
    },
    load_leave_balance(id) {
      this.leave_tblisBusy = true;
      this.$http.get("api/LeaveBalance/" + id).then(function(response) {
        this.leave_items = response.body;
        this.leave_totalRows = this.leave_items.length;
        this.leave_tblisBusy = false;
      });
    },
    openModalAddLeave() {
      this.$bvModal.show("ModalAddLeave");
    },
    btnAddLeave() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.leave_add.multiple = 0;
          this.leave_add.employee_id = this.item_row_click.id;

          this.$root.$emit("pageLoading");
          this.$http
            .post("api/LeaveBalance", this.leave_add)
            .then(response => {
              this.$root.$emit("pageLoaded");
              //console.log(response.body);
              swal("Notification", "Added successfully", "success");
              this.leave_items = response.body;
              this.leave_totalRows = this.leave_items.length;
              this.leave_add = {
                employee_id: "",
                leave_type_id: "",
                enroll_year: "",
                balance: "0",
                availed: "0",
                accrued: "0"
              };
              this.$bvModal.hide("ModalAddLeave");
            })
            .catch(response => {
              this.$root.$emit("pageLoaded");
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              });
            });
        }
      });
    },
    btnAddLeaveMultiple() {
      console.log(this.item_selected);
      this.$validator.validateAll().then(result => {
        if (result) {
          this.leave_add.multiple = 1;
          this.leave_add.employees = this.item_selected;

          this.$root.$emit("pageLoading");
          this.$http
            .post("api/LeaveBalance", this.leave_add)
            .then(response => {
              this.$root.$emit("pageLoaded");
              console.log(response.body);

              swal("Notification", "Added successfully", "success");
              this.leave_add = {
                employee_id: "",
                leave_type_id: "",
                enroll_year: "",
                balance: "0",
                availed: "0",
                accrued: "0"
              };
              this.$bvModal.hide("ModalAddLeave");
              this.uncheckAllSelectedEmp();
            })
            .catch(response => {
              this.$root.$emit("pageLoaded");
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              });
            });
        }
      });
    },
    btnEditLeave() {
      this.$validator.validateAll().then(result => {
        if (result) {
          swal({
            title: "Are you sure?",
            text: "Do you want to Update this item?",
            icon: "warning",
            buttons: true,
            dangerMode: true
          }).then(update => {
            this.tblisBusy = true;
            if (update) {
              this.$root.$emit("pageLoading");
              this.$http
                .put("api/LeaveBalance/" + this.leave_edit.id, this.leave_edit)
                .then(response => {
                  this.$root.$emit("pageLoaded");
                  this.leave_items = response.body;
                  swal("Info!", "Update successfully", "success");
                  this.$bvModal.hide("ModalEditLeave");
                  this.tblisBusy = false;
                })
                .catch(response => {
                  this.$root.$emit("pageLoaded");
                  swal({
                    title: "Error",
                    text: response.body.error,
                    icon: "error",
                    dangerMode: true
                  });
                });
            }
          });
        }
      });
    },
    //sched functions-------------------------------------------------------------------------
    openModalSched(item) {
      this.$bvModal.show("ModalSched");
      this.sched_items = [];
      this.pay_period_select = "";
      this.item_row_click = item;
      this.uncheckAllSelectedEmp();
    },
    pay_period_onchange() {
      this.sched_tblisBusy = true;

      this.$root.$emit("pageLoading");
      this.$http
        .get(
          "api/getDTR/" + this.pay_period_select + "/" + this.item_row_click.id
        )
        .then(function(response) {
          console.log(response.body);

          this.$root.$emit("pageLoaded");
          this.sched_items = response.body;
          this.sched_tblisBusy = false;
          this.sched_totalRows = this.sched_items.length;
          this.sched_currentPage = 1;
        });
    },
    pay_period_onchange_add() {
      this.$root.$emit("pageLoading");
      this.$http
        .put(
          "api/getDTR_add/" + this.item_row_click.id,
          this.pay_period_select_add
        )
        .then(function(response) {
          this.$root.$emit("pageLoaded");
          console.log(response.body);
          this.sched_items_add = response.body;
        });
    },
    btnAddSched() {
      swal({
        title: "Are you sure?",
        text: "Do you really want to submit this schedule?",
        icon: "warning",
        buttons: true,
        dangerMode: true
      }).then(add => {
        if (add) {
          this.$root.$emit("pageLoading");
          this.$http
            .post("api/Dtr", this.sched_items_add)
            .then(response => {
              this.$root.$emit("pageLoaded");
              console.log(response.body);

              swal("Notification", "Added successfully", "success");
              //set new val
              this.sched_items_add = [];
              this.$bvModal.hide("ModalAddSched");
            })
            .catch(response => {
              this.$root.$emit("pageLoaded");
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              });
            });
        }
      });
    },
    btnAddSchedMultiple() {
      console.log(this.item_selected);

      swal({
        title: "Are you sure?",
        text: "Do you really want to submit this schedule?",
        icon: "warning",
        buttons: true,
        dangerMode: true
      }).then(add => {
        if (add) {
          this.$root.$emit("pageLoading");
          var temp = {
            scheds: this.sched_items_add,
            employees: this.item_selected
          };
          this.$http
            .post("api/Dtr/storeMultiple", temp)
            .then(response => {
              this.$root.$emit("pageLoaded");
              swal("Notification", "Added successfully", "success");
              //set new val
              this.sched_items_add = [];
              this.$bvModal.hide("ModalAddSched");
              this.uncheckAllSelectedEmp();
            })
            .catch(response => {
              this.$root.$emit("pageLoaded");
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              });
            });
        }
      });
    },
    //approver
    btnMakeApprover() {
      swal({
        title: "Are you sure?",
        text: "Do you really want to make this employee to be an approver?",
        icon: "warning",
        buttons: true,
        dangerMode: true
      }).then(add => {
        if (add) {
          this.$root.$emit("pageLoading");
          var approver = {
            employee_id: this.item_edit.bioID
          };
          this.$http
            .post("api/Approver", approver)
            .then(response => {
              this.$root.$emit("pageLoaded");
              console.log(response.body);
              swal("Notification", "Approver added successfully", "success");
            })
            .catch(response => {
              this.$root.$emit("pageLoaded");
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              });
            });
        }
      });
    },
    openModalApprover(item) {
      this.approve_tblisBusy = true;
      this.$http
        .get("api/EmployeeApprover/" + item.id)
        .then(function(response) {
          //console.log(response.body);
          this.approve_items = response.body;
          this.approve_tblisBusy = false;
          this.approve_totalRows = this.approve_items.length;
        });

      this.$bvModal.show("modalManageApprover");
      this.item_row_click = item;
      this.$http.get("api/Approver").then(function(response) {
        this.approvers = response.body;
      });
    },
    openModal(item, modalName) {
      //ModalManagePayslip
      this.$bvModal.show(modalName);
      this.item_row_click = item;
      console.log(this.item_row_click);
    },
    customeApproverName(item) {
      return `${item.employee.first_name} ${item.employee.last_name}`;
    },
    btnAddApproverOK() {
      swal({
        title: "Are you sure?",
        text: "Do you really want to add this approver to this employee?",
        icon: "warning",
        buttons: true,
        dangerMode: true
      }).then(add => {
        if (add) {
          this.$root.$emit("pageLoading");
          this.approve_tblisBusy = true;
          this.approve_add.employee_id = this.item_row_click.id;

          this.$http
            .post("api/EmployeeApprover", this.approve_add)
            .then(response => {
              this.$root.$emit("pageLoaded");
              console.log(response.body);
              this.approve_items = response.body;
              this.approve_tblisBusy = false;
              this.approve_totalRows = this.approve_items.length;
              swal("Notification", "Approver added successfully", "success");
            })
            .catch(response => {
              this.$root.$emit("pageLoaded");
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              });
            });
        }
      });
    },
    updateLog() {
      this.$http
        .post("api/updateRALog")
        .then(response => {
          //console.log(response.body);
          swal("Notification", "Updated", "success");
        })
        .catch(response => {
          swal({
            title: "Error",
            text: response.body.error,
            icon: "error",
            dangerMode: true
          });
        });
    },
    chkbox_clicked(row) {
      //
      //

      if (row.rowSelected) {
        this.$refs.selectableTable.selectRow(row.index);

        //   this.checked_count++;
        //   console.log(this.checked_count);
      } else {
        //   this.checked_count--;
        //   console.log(this.checked_count);
        this.$refs.selectableTable.unselectRow(row.index);
      }
    },
    uncheckAllSelectedEmp() {
      // this.items.forEach(item => {
      //   item.chk = false;
      // });
      // this.checked_count = 0;

      this.$refs.selectableTable.clearSelected();
    },
    btnSubmitResetPassword() {
      if (this.item_edit.password == this.item_edit.repassword) {
        swal({
          title: "Are you sure?",
          text: "Do you really want to reset this password?",
          icon: "warning",
          buttons: true,
          dangerMode: true
        }).then(update => {
          this.$root.$emit("pageLoading");
          this.tblisBusy = true;
          ModalResetPassword;
          this.$bvModal.hide("ModalResetPassword");
          this.$bvModal.hide("ModelAddEmployee");
          if (update) {
            this.$http
              .post("api/user/ResetPassword", this.item_edit)
              .then(response => {
                this.$root.$emit("pageLoaded");
                swal("Update!", "Update successfully", "success");
                this.tblisBusy = false;
              })
              .catch(response => {
                this.$root.$emit("pageLoaded");
                swal({
                  title: "Error",
                  text: response.body.error,
                  icon: "error",
                  dangerMode: true
                });
              });
          }
        });
      } else {
        swal("Password not match", "", "info");
      }
    },
    btnUpdateRoles() {
      swal({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true
      }).then(update => {
        if (update) {
          this.$root.$emit("pageLoading");
          this.editRoles.user_id = this.user.id;
          this.editRoles.user_name = this.user.email;
          this.$http
            .post("api/user/updateRoles", this.editRoles)
            .then(response => {
              this.$root.$emit("pageLoaded");
              this.items = response.body;
              swal("Updated", "", "success");
              this.$bvModal.hide("modalRoles");
            })
            .catch(response => {
              this.$root.$emit("pageLoaded");
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              });
            });
        }
      });
    },
    selectAllRows(chk) {
      if (chk) {
        this.$refs.selectableTable.selectAllRows();
      } else {
        this.$refs.selectableTable.clearSelected();
      }
    },
    onRowSelected(items) {
      this.item_selected = items;
    }
  }
};
</script>
<style scoped>
.textLabel {
  margin-top: 9px;
  font-size: 12px;
}
.rowFields {
  margin-top: 15px;
}

.rowFields1 {
  margin: 20px;
  font-size: 12px;
}

.modal-content,
modal-header {
  border: 1px solid red;
}

.margin-right-10 {
  margin-right: 10px;
}
</style>
