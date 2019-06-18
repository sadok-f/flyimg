# Deploy Flyimg on Minishift
In this tutorial we're going to explore how to deploy and run a Flyimg application in [Minishift](https://www.okd.io/minishift/) (Run OpenShift locally).

## Minishift
### Install Minishift
    brew cask install minishift

### Set VirtualBox as VM-Driver
    minishift config set vm-driver virtualbox

### Start Minishift
    minishift start --memory=6GB

### Export oc command
    minishift oc-env
    eval (minishift oc-env)

### Create Dev Project
    oc new-project devproject --display-name="Dev Project"

### Login as Admin
    oc login -u system:admin


## Jenkins
### Install Jenkins
    oc new-app jenkins-ephemeral
NOTE:
It might the first build for Jenkins fail and it needs to be deployed again, via the Web Console -> Deployment -> Jenkins -> Delpoy

### Get available routes
    oc get route

Login to Jenkins with your Minishift developer credentials


## Deploy Flyimg on Minishift using Jenkins Pipeline
### Install anyuid addon
Allows authenticated users to run images under a non pre-allocated UID

    minishift addon apply anyuid

### Clone Flyimg repo
    git clone https://github.com/sadok-f/flyimg.git
    cd flyimg

### Create the base docker image
    oc create -f minishift/oc-docker-app-image.yaml
NOTE:
It might be the image not properly created, you can replace it if there's any errors:

    oc replace -f minishift/oc-docker-app-image.yaml

### Create jenkins pipeline
    oc create -f minishift/oc-flyimg-pipeline.yaml

### Start the pipeline
    oc start-build oc-flyimg-pipeline


## Prometheus
### Create prometheus-data folder in the host
    minishift ssh 'sudo mkdir -p /var/lib/prometheus-data'
    minishift ssh 'sudo chmod 777 -R /var/lib/prometheus-data'
    minishift ssh 'sudo chcon -Rt svirt_sandbox_file_t /var/lib/prometheus-data/'


### Get the router password
    oc set env dc router -n default --list|grep STATS_PASSWORD|awk -F"=" '{print $2}'

### Delpoy Prometheus
    oc new-app -f minishift/prometheus.yaml --param ROUTER_PASSWORD=F779sdmZ1C --param NAMESPACE=devproject

### Since Prometheus needs to use a local disk to write its metrics add the privileged SCC to the prometheus service account:
    oc adm policy add-scc-to-user privileged system:serviceaccount:devproject:prometheus

### Make sure your Prometheus pod is running:
    oc get pod -o wide

## Node Exporter
### Create folder
    minishift ssh 'sudo mkdir -p /var/lib/node_exporter/textfile_collector'
    minishift ssh 'sudo chmod 777 -R /var/lib/node_exporter/textfile_collector'
    minishift ssh 'sudo chcon -Rt svirt_sandbox_file_t /var/lib/node_exporter/textfile_collector'

### Elevated Access
    oc adm policy add-scc-to-user privileged system:serviceaccount:devproject:default

### Instantiate the Template
    oc new-app -f minishift/node-exporter.yaml

## Grafana
    oc new-app -f minishift/grafana.yaml
