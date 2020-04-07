<template>
    <div>
        <el-table
                :data="tableData"
                border
                :sortable="true"
                sort-by="created_at"
                style="width: 100%"
                ref="editTable"
        >
            <el-table-column
                    prop="created_at"
                    label="日期"
                    width="250">
            </el-table-column>
            <el-table-column
                    prop="value"
                    label="值"
                    width="350">
            </el-table-column>
            <el-table-column
                    prop="message"
                    label="报警信息">
            </el-table-column>
        </el-table>
    </div>
</template>

<script>
    import axios from 'axios'
    export default {
        name: "Alarm",

        data: function () {
            return {

                tableData: []

            }
        },
        created() {
            this.fetchData();
        },
        methods: {
            fetchData() {
                this.loading = true;
                axios
                    .get('/api/alarm')
                    .then(response => {
                        this.tableData = response.data.data;
                        console.log(response);
                    });
            }
        }
    }
</script>

