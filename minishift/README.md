### instal Minishift
    brew cask install minishift

### Set VirtualBox as VM-Driver
    minishift config set vm-driver virtualbox

### Start Minishift
    minishift start


minishift oc-env
eval (minishift oc-env)

    oc login -u system:admin
# oc whoami

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
    oc new-app -f minishift/prometheus.yaml -p NAMESPACE=devproject -n devproject

## Grafana
    oc new-app -f minishift/grafana.yaml -n devproject