document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.getElementById("data-table");
    const modal = document.getElementById("editModal");
    const deleteModal = document.getElementById("deleteModal"); 
    const closeBtn = document.getElementsByClassName("close")[0];
    const editForm = document.getElementById("editForm");
    const deleteConfirmBtn = document.getElementById("deleteConfirmBtn");
    const deleteCancelBtn = document.getElementById("deleteCancelBtn"); 
    let deleteId = null;

    // ฟังก์ชันโหลดข้อมูลทั้งหมดหรือค้นหาตามคำค้นหา
    function loadData(searchQuery = '') {
        let url = `../backend/editDatabase.php`; 
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
                    tableBody.innerHTML = `<tr><td colspan="7">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>`;
                }
            })
            .catch(error => {
                console.error("Error loading data:", error);
                tableBody.innerHTML = `<tr><td colspan="7">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>`;
            });
    }

    // ฟังก์ชันแสดงข้อมูลในตาราง
    function renderTable(data) {
        if (data.length > 0) {
            tableBody.innerHTML = "";
            data.forEach(row => {
                // ตรวจสอบว่า em_timestamp เป็น null หรือไม่
                let formattedDate = "";
                if (row.em_timestamp) {
                    const timestamp = new Date(row.em_timestamp); // แปลงเป็น Date object
                    formattedDate = timestamp.toLocaleDateString("th-TH", {
                        day: "2-digit",
                        month: "2-digit",
                        year: "numeric"
                    });
                }

                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${row.em_id}</td>
                    <td>${formattedDate}</td> 
                    <td>${row.em_roomNo}</td>
                    <td>${row.em_meterID}</td>
                    <td>${row.em_sum}</td>
                    <td>${row.em_addNumber}</td>
                    <td>
                        <button onclick="editRow(${row.em_id}, '${row.em_roomNo}', '${row.em_meterID}', '${row.em_addNumber}')" class="edit-btn"><i class="fas fa-pencil-alt"></i></button>
                        <button onclick="showDeleteModal(${row.em_id})" class="delete-btn"><i class="fas fa-trash-alt"></i></button>
                    </td>
                `;
                tableBody.appendChild(tr);
            });
        } else {
            tableBody.innerHTML = `<tr><td colspan="7">ไม่พบข้อมูลที่ค้นหา</td></tr>`;
        }
    }



    // ฟังก์ชันค้นหาอัตโนมัติเมื่อพิมพ์
    document.getElementById("searchInput").addEventListener("input", function () {
        const search = this.value.trim();

        if (!search) {
            // ถ้าไม่มีคำค้นหาให้กลับไปหน้า editdatabase.html
            window.location.href = "editDatabase.html";
            return;
        }

        // ส่งคำค้นหาไปยัง PHP และดึงข้อมูล
        fetch(`../backend/editDatabase.php?search=${encodeURIComponent(search)}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    tableBody.innerHTML = `<tr><td colspan="7">${data.error}</td></tr>`;
                } else {
                    // หากพบข้อมูล ให้แสดงข้อมูลในตาราง
                    renderTable(data);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("เกิดข้อผิดพลาดในการค้นหา");
            });
    });

    // ฟังก์ชันแก้ไขข้อมูล
    window.editRow = function (id, roomNo, meterID, addNumber) {
        document.getElementById("edit_id").value = id;
        document.getElementById("edit_roomNo").value = roomNo;
        document.getElementById("edit_meterID").value = meterID;
        document.getElementById("edit_addNumber").value = addNumber;
        modal.style.display = "block";
    };

    // ปิด Modal
    closeBtn.onclick = function () {
        modal.style.display = "none";
    };

    window.onclick = function (event) {
        if (event.target == modal || event.target == deleteModal) {
            modal.style.display = "none";
            deleteModal.style.display = "none";
        }
    };

    // จัดการการส่งฟอร์มแก้ไข
    editForm.onsubmit = function (e) {
        e.preventDefault();
        const formData = new FormData(editForm);
        const data = {};
        formData.forEach((value, key) => data[key] = value);

        fetch("../backend/updateData.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(data),
        })
            .then(response => response.json())
            .then(result => {
                if (result.status === "success") {
                    alert(result.message);
                    modal.style.display = "none";
                    loadData();
                } else {
                    alert(result.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("เกิดข้อผิดพลาดในการอัพเดทข้อมูล");
            });
    };

    // ฟังก์ชันแสดง Modal สำหรับลบข้อมูล
    window.showDeleteModal = function (id) {
        deleteId = id; 
        deleteModal.style.display = "block";
    };

    // ฟังก์ชันยืนยันการลบ
    deleteConfirmBtn.onclick = function () {
        fetch(`../backend/deleteData.php`, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `id=${deleteId}`,
        })
            .then(response => response.text())
            .then(result => {
                alert(result);
                deleteModal.style.display = "none"; 
                loadData();
            })
            .catch(error => {
                console.error("Error:", error);
                alert("เกิดข้อผิดพลาดในการลบข้อมูล");
                deleteModal.style.display = "none"; 
            });
    };

    // ฟังก์ชันยกเลิกการลบ
    deleteCancelBtn.onclick = function () {
        deleteModal.style.display = "none"; 
    };
      
    loadData(); 
});
