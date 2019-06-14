pipeline {
    agent none
    stages {
        stage('Build') {
            agent any
            steps {
                checkout scm
            }
        }
        stage('Docker build') {
             agent any
            steps {
                 sh 'docker build . -t flyimg'
               
            }
        }
        stage('Docker push') {
             agent any
            steps {
                 sh 'docker push flyimg'
               
            }
        }
    }
}
