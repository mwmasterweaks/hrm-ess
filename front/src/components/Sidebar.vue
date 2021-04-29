<template>
  <sidebar-menu :menu="menu" width="300px" :collapsed="true" :hideToggle="true" />
</template>


<script>
import { SidebarMenu } from "vue-sidebar-menu";
export default {
  components: {
    SidebarMenu
  },
  data() {
    return {
      menu: [
        {
          header: true,
          title: "Main Navigation",
          hiddenOnCollapse: true
        },
        {
          title: "HOME",
          icon: "fas fa-home",
          child: [
            {
              href: "/MyApplications",
              title: "My Applications"
            },
            {
              href: "/MyPayslip",
              title: "My Payslip"
            },
            {
              href: "/MyApprovers",
              title: "My Approvers"
            },
            {
              href: "/MyCalendar",
              title: "My Calendar"
            }
          ]
        },
        {
          title: "APPLICATIONS",
          icon: "fas fa-scroll",
          child: [
            {
              href: "/ApproveApplications",
              title: "Approve Applications",
              badge: {
                text: "5",
                class: "vsm--badge_default"
                // attributes: {}
                // element: 'span'
              }
            },
            {
              href: "/Leave",
              title: "Leave"
            },
            {
              href: "/Overtime",
              title: "Overtime"
            },
            {
              href: "/OfficialBusiness",
              title: "Official Business"
            },
            {
              href: "/ChangeofShift",
              title: "Change of Shift"
            },
            {
              href: "/ChangeRestDay",
              title: "Change Rest Day"
            },
            {
              href: "/OffsetRequest",
              title: "Offset Request"
            },
            {
              href: "/MissingTimeLogs",
              title: "Missing Time Logs"
            },
            {
              href: "/ManualAttendance",
              title: "Manual Attendance"
            }
          ]
        },
        {
          title: "INQUIRY",
          icon: "fas fa-phone",
          child: [
            {
              href: "/MyLeaveBalance",
              title: "My Leave Balance"
            },
            {
              href: "/MyDTR",
              title: "My Daily Time Record"
            }
          ]
        }
      ],
      user: {},
      roles: []
    };
  },
  created() {
    this.roles = this.$global.getRoles();
    this.user = this.$global.getUser();
    this.load();
  },
  mounted() {
    this.$root.$on("Sidebar", () => {
      //console.log("CALL1");
      this.load();
    });
  },
  methods: {
    load() {
      var id = this.user.employee_id;
      this.$http.get("api/getToApprove/" + id).then(function(response) {
        //console.log(response.body.length);
        //save to global
        if (response.body.length > 0) {
          this.menu[2].child[0].badge.text = response.body.length;
        } else {
          this.menu[2].child.shift();
        }
        //this.items = response.body;
        //this.tblisBusy = false;
      });

      if (this.roles.hr) {
        this.menu[4] = {
          title: "MANAGEMENT",
          icon: "fas fa-user-cog",
          child: [
            {
              href: "/ManageEmployee",
              title: "Employee"
            },
            {
              href: "/ShiftSchedule",
              title: "Shift Schedule"
            },
            {
              href: "/ManageGroup",
              title: "Group"
            },
            {
              href: "/ManagePosition",
              title: "Position"
            },
            {
              href: "/ManageDepartment",
              title: "Department"
            },
            {
              href: "/ManagePayPeriod",
              title: "Pay Period"
            }
          ]
        };

        if (this.roles.create_rate)
          this.menu[4].child.push({
            href: "/ManageRates",
            title: "Rates"
          });
        if (this.roles.create_branch)
          this.menu[4].child.push({
            href: "/ManageBranch",
            title: "Branch"
          });
        if (this.roles.create_leave)
          this.menu[4].child.push({
            href: "/ManageLeaveType",
            title: "Leave type"
          });
        if (this.roles.create_calendar)
          this.menu[4].child.push({
            href: "/ManageCalendar",
            title: "Calendar"
          });
      }
    }
  }
};
</script>
