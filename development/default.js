function patientApp() {
  return {
    patients: [],
    currentPatient: {
      name: "",
      age: "",
      address: "",
      gender: "Male",
      phone: "",
      dob: "",
      compliant: "",
      treatments: "",
      investigation: "",
    },

    isEditing: false,
    isAdding: true,
    isUpdating: true,
    showAddPatientModal: false,
    showValidationError: true,

    fetchPatients() {
      console.log("fetchPatients: ...");
      fetch("api.php")
        .then((response) => response.json())
        .then((data) => {
          this.patients = data;
          console.log("Patients fetched:");
          console.table(this.patients);
        });
    },

    addPatient() {
      console.log(
        `addPatient: isAdding=${this.isAdding} isUpdating=${this.isUpdating} patient=`,
        this.currentPatient
      );

      fetch("api.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(this.currentPatient),
      })
        .then((response) => response.json())
        .then(() => {
          this.fetchPatients();
          this.initCurrentPatient();
        });
    },

    updatePatient(patient) {
      console.log(
        `editPatient: isAddindg=${this.isAdding} isUpdating=${this.isUpdating} patient=`,
        patient
      );
      fetch(`api.php?id=${patient.id}`, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ ...patient }),
      }).then(() => this.fetchPatients());
    },

    submitPatient() {
      console.log("submitPatient:", this.currentPatient);

      if (this.isAdding) {
        this.addPatient();
      }
      if (this.isUpdating) {
        this.updatePatient(this.currentPatient);
      }
      this.currentPatient = {};
      this.isAdding = false;
      this.isUpdating = false;
      this.showAddPatientModal = false;
    },

    showEditPatient(patient) {
      console.log("showEditPatient:", this.currentPatient);
      fetch(`api.php?id=${this.currentPatient.id}`, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(this.currentPatient),
      })
        .then((response) => response.json())
        .then(() => {
          this.fetchPatients();
          this.currentPatient = {};
          this.isEditing = false;
          this.showAddPatientModal = false;
        });
    },

    deletePatient(id) {
      console.log("Deleting patient with ID:", id);
      fetch(`api.php?id=${id}`, { method: "DELETE" }).then(() =>
        this.fetchPatients()
      );
    },

    showPatient(patient) {
      console.log("showPatient", patient);

      this.currentPatient = patient;
      console.table(patient);
    },

    showSubmitButton(patient) {
      return !patient.name || !patient.gender || !patient.dob;
    },
    initCurrentPatient() {
      this.currentPatient = {
        name: "",
        age: "",
        dob: "",
        gender: "Male",
        address: "",
        phone: "",
      };
    },
    init() {
      this.fetchPatients();
      this.initCurrentPatient();
      this.showAddPatientModal = false;
    },
  };
}
