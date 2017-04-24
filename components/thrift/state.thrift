namespace go state
namespace php state

struct Client {
     1: optional string tid, // controller id
     2: optional string uid, // user id
     3: optional string sid, // session id
     4: optional string cid, // connection id
     5: optional string tag  // client tag
}

service StateManager {
     void disconnect(
          1: Client client,
          2: string reason
     ),
     void send(
          1: Client client,
          2: string message
     ),
     list<Client> getClients(
          1: Client client,
     ),
     void setTag(
          1: Client client,
          2: string tag
     ),
     i32 stateCount(
          1: string db
     ),
     void stateSet(
          1: string db,
          2: string key,
          3: string val
     ),
     void stateDel(
          1: string db,
          2: string key
     ),
     string stateGet(
          1: string db,
          2: string key
     ),
     list<map<string, string>> stateGetByKey(
          1: string db,
          2: string key
     ),
     void stateCreateIndex(
          1: string db,
          2: string name,
          3: string pattern,
          4: string indextype
     ),
     list<map<string, string>> stateGetByIndex(
          1: string db,
          2: string name,
          3: map<string, string> params
     )
}