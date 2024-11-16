document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.getElementById("data-table");
    const modal = document.getElementById("editModal");
    const closeBtn = document.getElementsByClassName("close")[0];
    const editForm = document.getElementById("editForm");
    const errorMessage = document.getElementById("error-message");

    // ฟังก์ชันโหลดข้อมูลทั้งหมด
    function loadData() {
        fetch("../backend/editDatabase.php")
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
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${row.em_id}</td>
                    <td>${row.em_timestamp}</td>
                    <td>${row.em_roomNo}</td>
                    <td>${row.em_meterID}</td>
                    <td>${row.em_addNumber}</td>
                    <td>${row.em_sum}</td>
                    <td>
                        <button onclick="editRow(${row.em_id}, '${row.em_roomNo}', '${row.em_meterID}', '${row.em_addNumber}')" class="edit-btn">แก้ไข</button>
                        <button onclick="deleteRow(${row.em_id})" class="delete-btn">ลบ</button>
                    </td>
                `;
                tableBody.appendChild(tr);
            });
        } else {
            tableBody.innerHTML = `<tr><td colspan="7">ไม่มีข้อมูล</td></tr>`;
        }
    }

    // ฟังก์ชันค้นหา
    window.searchData = function() {
        const search = document.getElementById("searchInput").value.trim();
        if (errorMessage) {
            errorMessage.style.display = "none";
        }

        if (!search) {
            if (errorMessage) {
                errorMessage.textContent = "กรุณาใส่คำค้นหา";
                errorMessage.style.display = "block";
            }
            return;
        }

        fetch(`../backend/searchData.php?search=${encodeURIComponent(search)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    renderTable(data);
                } else {
                    if (errorMessage) {
                        errorMessage.textContent = "ไม่พบข้อมูลที่ค้นหา";
                        errorMessage.style.display = "block";
                    }
                }
            })
            .catch(error => {
                console.error("Error searching data:", error);
                if (errorMessage) {
                    errorMessage.textContent = "เกิดข้อผิดพลาดในการค้นหา";
                    errorMessage.style.display = "block";
                }
            });
    };

    // ฟังก์ชันแก้ไขข้อมูล
    window.editRow = function(id, roomNo, meterID, addNumber) {
        document.getElementById("edit_id").value = id;
        document.getElementById("edit_roomNo").value = roomNo;
        document.getElementById("edit_meterID").value = meterID;
        document.getElementById("edit_addNumber").value = addNumber;
        modal.style.display = "block";
    };

    // ปิด Modal
    closeBtn.onclick = function() {
        modal.style.display = "none";
    };

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };

    // จัดการการส่งฟอร์มแก้ไข
    editForm.onsubmit = function(e) {
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

    // ฟังก์ชันลบข้อมูล
    window.deleteRow = function(id) {
        if (confirm("คุณต้องการลบข้อมูลนี้ใช่หรือไม่?")) {
            fetch(`../backend/deleteData.php`, {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `id=${id}`,
            })
                .then(response => response.text())
                .then(result => {
                    alert(result);
                    loadData();
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("เกิดข้อผิดพลาดในการลบข้อมูล");
                });
        }
    };

    // โหลดข้อมูลครั้งแรก
    loadData();
});
