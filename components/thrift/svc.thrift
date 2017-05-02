namespace go svc
namespace php svc

struct Instance {
  1: string pid,
  2: string output,
  3: string startTime,
  4: string stopTime,
  5: string serviceName
}

struct Service {
  1: string name,
  2: string commandPath,
  3: string command,
  4: string action,
  5: string schedule = "manual",
  6: string period,
  7: string instanceMode = "single",
  8: string singleInstanceAction = "wait",
  9: string status = "draft",
  10: string lastRun,
  11: optional map<string, string> view,
  12: optional map<string, Instance> runningInstances,
  13: optional list<Instance> stoppedInstances
}

exception InstanceFailed {
  1: Instance instance,
  2: string why
}

service ServiceManager {
  void add (
    1: Service svc
  ),
  void remove (
    1: string name
  ),
  string start (
    1: string name,
    2: string params
  ),
  void stop (
    1: string name
  ),
  void stopInstance (
    1: string pid
  ),
  Instance getInstance (
    1: string pid
  ),
  Service getService (
    1: string name
  ),
  void setView(
    1: string name,
    2: string key,
    3: string value
  ),
  map<string, Service> getAllServices(),
  string cwd(),
  void quit()
}