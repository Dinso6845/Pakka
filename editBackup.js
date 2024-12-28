document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.getElementById("data-table");
    // const modal = document.getElementById("editModal");
    // const deleteModal = document.getElementById("deleteModal");
    // const closeBtn = document.getElementsByClassName("close")[0];
    // const editForm = document.getElementById("editForm");
    // const deleteConfirmBtn = document.getElementById("deleteConfirmBtn");
    // const deleteCancelBtn = document.getElementById("deleteCancelBtn");
    const tableHead = document.querySelector("thead");
    const currentDate = new Date();
    const currentMonth = currentDate.getMonth();
    // const passwordModal = document.getElementById("passwordModal");
    // const passwordInput = document.getElementById("passwordInput");
    // const submitPasswordBtn = document.getElementById("submitPasswordBtn");
    // const correctPassword = "jj1234";
    // const closePasswordModalBtn = document.getElementById("closePasswordModalBtn");
    // let deleteId = null;
    // let currentAction = null;

    function getMonthName(monthIndex) {
        const months = [
            "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
            "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
        ];
        return months[monthIndex];
    }

    // เปลี่ยนชื่อคอลัมน์ให้แสดงเดือน
    function updateTableHeaders() {
        const monthNames = [
            getMonthName(currentMonth - 4),
            getMonthName(currentMonth - 3),
            getMonthName(currentMonth - 2),
            getMonthName(currentMonth - 1),
            getMonthName(currentMonth)
        ];

        const thElements = tableHead.querySelectorAll("th");
        thElements[3].textContent = `เดือน ${monthNames[0]}`;
        thElements[4].textContent = `เดือน ${monthNames[1]}`;
        thElements[5].textContent = `เดือน ${monthNames[2]}`;
        thElements[6].textContent = `เดือน ${monthNames[3]}`;
        thElements[7].textContent = `เดือน ${monthNames[4]}`;
    }

    updateTableHeaders();

    function loadData(searchQuery = '') {
        let url = `../backend/editBackup.php`;
        if (searchQuery) {
            url += `?search=${encodeURIComponent(searchQuery)}`;
        }
    
        fetch(url)
            .then(response => response.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    renderTable(data);
                } catch (error) {
                    console.error("Error parsing JSON:", error);
                    tableBody.innerHTML = `<tr><td colspan="10">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>`;
                }
            })
            .catch(error => {
                console.error("Error loading data:", error);
                tableBody.innerHTML = `<tr><td colspan="10">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>`;
            });
    }
    

    function renderTable(data) {
        if (data.length > 0) {
            tableBody.innerHTML = "";
            data.forEach(row => {
                let formattedDate = "";
                if (row.DatePoint && row.DatePoint !== '0000-00-00 00:00:00') {
                    const timestamp = new Date(row.DatePoint);
                    formattedDate = timestamp.toLocaleDateString("th-TH", {
                        day: "2-digit",
                        month: "2-digit",
                        year: "numeric"
                    });
                } else {
                    formattedDate = '00/00/0000';
                }

                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${formattedDate}</td> 
                    <td>${row.Roomno}</td>
                    <td>${row.SN}</td>
                    <td>${row.Meter09}</td>
                    <td>${row.Meter10}</td>
                    <td>${row.Meter11}</td>
                    <td>${row.Meter12}</td>
                    <td>${row.MonthElectricity}</td>
                    <td>${row.unit}</td>
                    <td>${row.percentage_change ? parseFloat(row.percentage_change).toFixed(2) : '0.00'}% 
                    
                        <span class="warning ${Math.abs(row.percentage_change || 0) > 20 ? 'red' : 'green'}">
                            ${Math.abs(row.percentage_change || 0) > 20 ? 'ผิดปกติ' : 'ปกติ'}
                        </span>

                    </td>


                       `;   
                tableBody.appendChild(tr);
                // <td>${row.em_id}</td>
                // <td>
                //     <button onclick="requestEdit(${row.em_id}, '${row.em_roomNo}', '${row.em_meterID}', '${row.em_addNumber}', '${row.em_addNumber1}', '${row.em_addNumber2}','${row.em_addNumber3}','${row.em_addNumberElectricity}')" class="edit-btn"><i class="fas fa-pencil-alt"></i></button>
                //     <button onclick="showDeleteModal(${row.em_id})" class="delete-btn"><i class="fas fa-trash-alt"></i></button>
                // </td>
            });
        } else {
            tableBody.innerHTML = `<tr><td colspan="10">ไม่พบข้อมูลที่ค้นหา</td></tr>`;
        }
    }

    document.getElementById("searchInput").addEventListener("input", function () {
        const search = this.value.trim();

        if (!search) {
            window.location.href = "editBackup.html";
            return;
        }

        fetch(`../backend/editBackup.php?search=${encodeURIComponent(search)}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    tableBody.innerHTML = `<tr><td colspan="7">${data.error}</td></tr>`;
                } else {
                    
                    renderTable(data);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("เกิดข้อผิดพลาดในการค้นหา");
            });
    });

    // ฟังก์ชันสำหรับการขอรหัสผ่านและแก้ไข
    // window.requestEdit = function (id, roomNo, meterID, addNumber, addNumber1, addNumber2, addNumber3) {
    //     currentAction = "edit";
    //     document.getElementById("passwordModal").style.display = "block";  // เปิด modal สำหรับรหัสผ่าน
    //     document.getElementById("edit_id").value = id;
    //     document.getElementById("edit_roomNo").value = roomNo;
    //     document.getElementById("edit_meterID").value = meterID;
    //     document.getElementById("edit_addNumber").value = addNumber;
    //     document.getElementById("edit_addNumber1").value = addNumber1;
    //     document.getElementById("edit_addNumber2").value = addNumber2;
    //     document.getElementById("edit_addNumber3").value = addNumber3;
    // };

    // ฟังก์ชันตรวจสอบรหัสผ่าน
    // submitPasswordBtn.onclick = function () {
    //     const enteredPassword = passwordInput.value.trim();
    //     if (enteredPassword === correctPassword) {
    //         passwordModal.style.display = "none";  // ปิด modal รหัสผ่าน
    //         if (currentAction === "edit") {
    //             modal.style.display = "block";  // เปิด modal แก้ไข
    //         } else if (currentAction === "delete") {
    //             deleteModal.style.display = "block";  // เปิด modal ยืนยันการลบ
    //         }
    //     } else {
    //         alert("รหัสผ่านไม่ถูกต้อง");
    //     }
    // };

    // closePasswordModalBtn.onclick = function () {
    //     passwordModal.style.display = "none";
    // };

    // ปิด Modal แก้ไข
    // closeBtn.onclick = function () {
    //     modal.style.display = "none";
    // };

    // window.onclick = function (event) {
    //     if (event.target == modal || event.target == deleteModal) {
    //         modal.style.display = "none";
    //         deleteModal.style.display = "none";
    //     }
    // };

    // editForm.onsubmit = function (e) {
    // e.preventDefault();
    // const formData = new FormData(editForm);
    // const data = {};
    // formData.forEach((value, key) => data[key] = value);

    // fetch("../backend/updateBackup.php", {
    //     method: "POST",
    //     headers: {
    //         "Content-Type": "application/json",
    //     },
    //     body: JSON.stringify(data),
    // })
    //     .then(response => {
    //         if (!response.ok) {
    //             throw new Error("Failed to update data: " + response.statusText);
    //         }
    //         return response.json();
    //     })
    //     .then(result => {
    //         if (result.status === "success") {
    //             alert(result.message);
    //             modal.style.display = "none";
    //             loadData();
    //         } else {
    //             alert(result.message);
    //         }
    //     })
    //     .catch(error => {
    //         console.error("Error:", error);
    //         alert("เกิดข้อผิดพลาดในการอัพเดทข้อมูล");
    //     });
    // };

    // ฟังก์ชันแสดง Modal สำหรับลบข้อมูล
    // window.showDeleteModal = function (id) {
    //     currentAction = "delete";  // ระบุว่าเป็นการลบ
    //     deleteId = id;
    //     document.getElementById("passwordModal").style.display = "block";  // เปิด modal สำหรับรหัสผ่าน
    // };

    // // ฟังก์ชันยืนยันการลบ
    // deleteConfirmBtn.onclick = function () {
    //     fetch(`../backend/deleteBackup.php`, {
    //         method: "DELETE",
    //         headers: {
    //             "Content-Type": "application/x-www-form-urlencoded",
    //         },
    //         body: `id=${deleteId}`,
    //     })
    //         .then(response => response.text())
    //         .then(result => {
    //             alert(result);
    //             deleteModal.style.display = "none";
    //             loadData();
    //         })
    //         .catch(error => {
    //             console.error("Error:", error);
    //             alert("เกิดข้อผิดพลาดในการลบข้อมูล");
    //             deleteModal.style.display = "none";
    //         });
    // };

    // // ฟังก์ชันยกเลิกการลบ
    // deleteCancelBtn.onclick = function () {
    //     deleteModal.style.display = "none";
    // };

    // const baseURL = "http://127.0.0.1/Electricity/frontend/editBackup.html";
    // const encodedURL = encodeURIComponent(baseURL);

    // window.history.replaceState({}, "", `?url=${encodedURL}`);


    loadData();
});
