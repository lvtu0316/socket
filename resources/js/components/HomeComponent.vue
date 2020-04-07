<template>
    <div id="home">
        <el-row :gutter="12">
            <el-col :span="8" v-for="(item,index) in items" :key="index">
                <el-card shadow="always" >
                    {{index}}:{{item}}
                </el-card>
            </el-col>
        </el-row>
        <el-divider></el-divider>
    </div>


</template>
<script>
    export default {
        name: 'home',
        data: function () {
            return {
                visible: false,
                items: [],
                alarms: [],
                tableData: [],
                isPlaying:false

            }
        },
        created() {
            this.initWebSocket()
        },
        methods: {
            initWebSocket() { //初始化weosocket
                let that = this;

                // 初始化客户端套接字并建立连接
                var socket = new WebSocket("ws://192.168.1.10:5200");

                // 连接建立时触发
                socket.onopen = function (event) {
                    const h = this.$createElement;

                    that.$message({
                        showClose: true,
                        message: '连接成功',
                        type: 'success'
                    });
                }

                // 接收到服务端推送时执行
                socket.onmessage = function (event) {

                    that.items = JSON.parse(event.data).data;
                    that.alarms = JSON.parse(event.data).alarm;
                    if (that.alarms !== undefined && that.alarms.length > 0) {
                        for (let key in that.alarms) {
                            that.timer = setTimeout(() => {
                                that.$notify.error({
                                    title: '告警',
                                    offset: 100,
                                    message: that.alarms[key]
                                });
                            }, 0)

                        }

                        console.log(that.tableData)

                        that.$options.methods.play()

                    }

                };

                // 连接关闭时触发
                socket.onclose = function (event) {
                    Vue.prototype.$message({
                        showClose: true,
                        message: '连接断开',
                        type: 'warning'
                    });

                }


            },


            getFormatDate() {
                var date = new Date();
                var month = date.getMonth() + 1;
                var strDate = date.getDate();
                if (month >= 1 && month <= 9) {
                    month = "0" + month;
                }
                if (strDate >= 0 && strDate <= 9) {
                    strDate = "0" + strDate;
                }
                var currentDate = date.getFullYear() + "-" + month + "-" + strDate
                    + " " + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
                return currentDate;
            },

            play(){
                this.audio = new Audio();
                this.audio.src = '/mp3/4031.mp3';
                let playPromise;
                playPromise = this.audio.play();
                console.log(playPromise)
                if (playPromise) {
                    playPromise.then(() => {
                        // 音频加载成功
                        // 音频的播放需要耗时

                    }).catch((e) => {
                        // 音频加载失败
                        console.error(e);
                    });
                }
            },


        }
    }
</script>