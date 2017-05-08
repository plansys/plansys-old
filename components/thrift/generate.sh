thrift -r -out server --gen go svc.thrift
thrift -r -out client --gen php svc.thrift 

thrift -r -out server --gen go state.thrift
thrift -r -out client --gen php state.thrift 
sed -i 's/UID/Uid/g' server/state/state.go