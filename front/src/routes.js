import Vue from "vue";
import VueRouter from "vue-router";

import Login from "./components/user/Login.vue";
import Register from "./components/user/Register.vue";
import Dashboard from "./components/Dashboard.vue";
// -----------------HOME-------------
import MyApplication from "./components/routes/HOME/MyApplication.vue";
import MyApprover from "./components/routes/HOME/MyApprover.vue";
import MyCalendar from "./components/routes/HOME/MyCalendar.vue";
import MyPayslip from "./components/routes/HOME/MyPayslip.vue";
// -----------------APPLICATIONS-------------
import ApproveApplications from "./components/routes/APPLICATIONS/ApproveApplications.vue";
import ChangeOfShift from "./components/routes/APPLICATIONS/ChangeOfShift.vue";
import ChangeRestDay from "./components/routes/APPLICATIONS/ChangeRestDay.vue";
import Leave from "./components/routes/APPLICATIONS/Leave.vue";
import ManualAttendance from "./components/routes/APPLICATIONS/ManualAttendance.vue";
import MissingTimeLogs from "./components/routes/APPLICATIONS/MissingTimeLogs.vue";
import OfficialBusiness from "./components/routes/APPLICATIONS/OfficialBusiness.vue";
import OffsetRequest from "./components/routes/APPLICATIONS/OffsetRequest.vue";
import Overtime from "./components/routes/APPLICATIONS/Overtime.vue";
// -----------------INQUIRY-------------
import MyDailyTimeRecord from "./components/routes/INQUIRY/MyDailyTimeRecord.vue";
import MyLeaveBalance from "./components/routes/INQUIRY/MyLeaveBalance.vue";
// -----------------MANAGEMENT-------------
import Calendar from "./components/routes/MANAGEMENT/Calendar.vue";
import Employee from "./components/routes/MANAGEMENT/Employee.vue";
import Group from "./components/routes/MANAGEMENT/Group.vue";
import Position from "./components/routes/MANAGEMENT/Position.vue";
import Department from "./components/routes/MANAGEMENT/Department.vue";
import PayPeriod from "./components/routes/MANAGEMENT/PayPeriod.vue";
import Branch from "./components/routes/MANAGEMENT/Branch.vue";
import Rates from "./components/routes/MANAGEMENT/Rates.vue";
import LeaveType from "./components/routes/MANAGEMENT/LeaveType.vue";

Vue.use(VueRouter);

const router = new VueRouter({
  routes: [
    {
      path: "/login",
      component: Login,
      meta: {
        forVisitors: true
      }
    },
    {
      path: "/register",
      component: Register,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/home",
      component: Dashboard,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/",
      component: Dashboard,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/MyApplications",
      component: MyApplication,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/MyApprovers",
      component: MyApprover,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/MyCalendar",
      component: MyCalendar,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/MyPayslip",
      component: MyPayslip,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ApproveApplications",
      component: ApproveApplications,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ChangeOfShift",
      component: ChangeOfShift,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ChangeRestDay",
      component: ChangeRestDay,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/Leave",
      component: Leave,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ManualAttendance",
      component: ManualAttendance,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/MissingTimeLogs",
      component: MissingTimeLogs,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/OfficialBusiness",
      component: OfficialBusiness,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/OffsetRequest",
      component: OffsetRequest,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/Overtime",
      component: Overtime,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/MyDTR",
      component: MyDailyTimeRecord,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/MyLeaveBalance",
      component: MyLeaveBalance,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ManageCalendar",
      component: Calendar,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ManageEmployee",
      component: Employee,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ManageGroup",
      component: Group,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ManageLeaveType",
      component: LeaveType,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ManagePosition",
      component: Position,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ManageDepartment",
      component: Department,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ManagePayPeriod",
      component: PayPeriod,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ManageBranch",
      component: Branch,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ManageRates",
      component: Rates,
      meta: {
        forAuth: true
      }
    }
  ],

  mode: "history"
});

export default router;
