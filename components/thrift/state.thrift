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
     void setState(
          1: string key,
          2: string val
     ),
     string getState(
          1: string key
     )
}