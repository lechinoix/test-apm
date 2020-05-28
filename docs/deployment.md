# Deploy the API to a staging env

Install:
- (kubectl)[https://kubernetes.io/docs/tasks/tools/install-kubectl/#install-kubectl]
- (gcloud sdk)[https://cloud.google.com/sdk/install]

Then create a Kubernetes cluster:
- Create a GCP project and enable the Kubernetes Engine API
- Create a cluster `gcloud container clusters create <CLUSTER_NAME>`
- Connect to this cluster `gcloud container clusters get-credentials <CLUSTER_NAME> --zone <CLUSTER_ZONE> --project <PROJECT_ID>`

Build and store your docker images:
- `docker build -t gcr.io/<PROJECT_ID>/php:<RELEASE_NUMBER> . --target api_php`
- `docker build -t gcr.io/<PROJECT_ID>/nginx:<RELEASE_NUMBER> . --target api_nginx`
- `docker push gcr.io/<PROJECT_ID>/php:<RELEASE_NUMBER>` and `docker push gcr.io/<PROJECT_ID>/nginx:<RELEASE_NUMBER>`

Modify the `nginx-deployment.yaml` and the `php-deplyment.yaml` files to put the docker images you've just pushed.

Run `kubectl appy -f devops/`.

Wait a few minutes, run `kubectl get ingress` you will find the IP of your staging.
