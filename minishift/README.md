### instal Minishift
    brew cask install minishift

### Set VirtualBox as VM-Driver
    minishift config set vm-driver virtualbox

### Start Minishift
    minishift start --memory=6GB

### Export oc command
    minishift oc-env
    eval (minishift oc-env)

### Login as Admin
    oc login -u system:admin

### Create Dev Project
    oc new-project devproject --display-name="Dev Project"

### Install anyuid addon
Allows authenticated users to run images under a non pre-allocated UID:

    minishift addon apply anyuid

## Jenkins

### Install Jenkins
    oc new-app jenkins-ephemeral

### Get available routes
    oc get route

### Clone Flyimg repo
    git clone https://github.com/sadok-f/flyimg.git
    cd flyimg

### Create the base docker image
    oc create -f minishift/oc-docker-app-image.yaml -n devproject

### Create jenkins pipeline
    oc create -f minishift/oc-flyimg-pipeline.yaml

### Start the pipeline
    oc start-build oc-flyimg-pipeline -n devproject


## Prometheus
### Create the prom secret
    oc create secret generic prom --from-file=minishift/prometheus.yml
 
### Create the prom-alerts secret
    oc create secret generic prom-alerts --from-file=minishift/alertmanager.yml
 
### Create the prometheus instance
    oc process -f https://raw.githubusercontent.com/openshift/origin/master/examples/prometheus/prometheus-standalone.yaml | oc apply -f -

## Prometheus
### Create prometheus-data folder in the host
    minishift ssh 'sudo mkdir -p /var/lib/prometheus-data'
    minishift ssh 'sudo chmod 777 -R /var/lib/prometheus-data'
    minishift ssh 'sudo chcon -Rt svirt_sandbox_file_t /var/lib/prometheus-data/'


### Get the router password
    oc set env dc router -n default --list|grep STATS_PASSWORD|awk -F"=" '{print $2}'

### Delpoy Prometheus
    oc new-app -f minishift/prometheus.yaml --param ROUTER_PASSWORD={REPLACE_WITH_ROUTER_PASSWORD} --param NAMESPACE=devproject

### Since Prometheus needs to use a local disk to write its metrics add the privileged SCC to the prometheus service account:
    oc adm policy add-scc-to-user privileged system:serviceaccount:devproject:prometheus

### Make sure your Prometheus pod is running:
    oc get pod -o wide

## Node Exporter
### Create folder
    minishift ssh 'sudo mkdir -p /var/lib/node_exporter/textfile_collector'

### Elevated Access
    oc adm policy add-scc-to-user privileged system:serviceaccount:devproject:default

### Instantiate the Template
    oc new-app -f minishift/node-exporter.yaml

## Grafana
    oc new-app -f minishift/grafana.yaml -n devproject