# How to Add Orderer in Hyperledger Fabric Version 2.5

##### blockchain

##### hyperledger

##### 31 August 2023

This guide aims to walk you through the process of adding an orderer to your existing Hyperledger Fabric network, specifically for version 2.5. One of the key components in a Fabric network is the orderer, **responsible for ordering transactions into blocks and distributing them across the network**. By adhering to the instructions outlined here, you'll be better equipped to achieve a seamless integration, meeting all version-specific requirements.

![Add Orderer Hyperledger Fabric](/media/blockchain/hyperledger/add-orderer-hyperledger-fabric-1.png)
*Diagram 1 shows the Blockchain network of current status*

> **Note:** Diagram 1 shows the Blockchain network of current status

## Prerequisites

Before we dive into the process, make sure you have:

- **Basic knowledge** of Blockchain and Hyperledger Fabric
- **Hyperledger Fabric v2.5** installed and running
- **Administrative access** to the Hyperledger network
- **Basic understanding** of YAML and Docker configurations
- **Familiarity** with Hyperledger Fabric and command-line tools

> **Important:** Make sure your existing network is up and running without issues before proceeding.

### Step 1: Initial Preparations

Set the initial Preparations such as backup and environment variables

- **Backup**: Make sure to backup your existing configuration and network setup.
- **Environment Variables**: Set the necessary environment variables.

<pre><code class="language-bash">
# Backup existing configurations
cp /path/to/config /path/to/config.bak
# Set environment variables
export FABRIC_CFG_PATH=/path/to/config
</code></pre>

> **Info:** Make backups in multiple secure locations.

### Step 2: Orderer Configuration

Add the new orderer service definition in the **docker-compose.yaml** file.

<pre><code class="language-bash">
# touch docker-compose.yaml
touch compose/docker-compose.yaml
</code></pre>

> **Reminder:** Always review changes **docker-compose.yaml** before applying them.

### Step 3: Configuration Updates

Modify your **configtx.yaml** to add the new orderer's configurations.

<pre><code class="language-bash">
# Update the configtx.yaml file
vi /path/to/configtx.yaml
</code></pre>

> **Reminder:** Always review changes **configtx.yaml** before applying them.

### Step 4: Deploy the New Orderer

Run the following commands to generate the necessary certificates and deploy the orderer.

<pre><code class="language-bash">
# Generate certificates for the new orderer
cryptogen generate --config=./crypto-config-orderer.yaml
# Run the configtxgen tool to generate the orderer genesis block.
configtxgen -profile YourProfile -outputBlock ./channel-artifacts/orderer.genesis.block
# Start the new orderer
docker-compose up -d new-orderer
</code></pre>

> **Info:** Check logs to verify that the orderer is started without errors.

### Step 5: Verification

To verify that your new orderer is working, execute the following command.Check the logs to ensure the new orderer is functioning as expected. Run some transactions to make sure the new orderer is participating in block creation.

<pre><code class="language-bash">
# Check the list of orderers in the network
docker ps -a
</code></pre>

> **Important:** Ensure the new orderer appears in the list.

![Add Orderer Hyperledger Fabric ](/media/blockchain/hyperledger/add-orderer-hyperledger-fabric-2.png)
*Diagram 2 shows the Blockchain network of current status*

## Troubleshooting

If the new orderer is not starting, check the logs for any errors. Ensure that the new orderer is reachable by the peers and existing orderers. If you encounter issues during this process, here are some common problems and their solutions:

- **Issue 1**: *description and solution*
- **Issue 2**: *description and solution*

## Conclusion

Congratulations, you've successfully added a new orderer to your Hyperledger Fabric v2.5 network! Adding a new orderer to your existing Hyperledger Fabric network is a critical task that can improve the network's performance and fault tolerance. By following these steps, you should be able to seamlessly integrate a new orderer into your Fabric network running version 2.5.

> **Link:** For further reading or advanced configurations, you may refer to the [official Hyperledger Fabric Documentation](https://hyperledger-fabric.readthedocs.io/).


<pre><code class="language-yaml">
orderer2.gov.my:
  container_name: orderer2.gov.my
  image: hyperledger/fabric-orderer:latest
  labels:
    service: hyperledger-fabric
  environment:
    - FABRIC_LOGGING_SPEC=INFO
    - ORDERER_GENERAL_LISTENADDRESS=0.0.0.0
    - ORDERER_GENERAL_LISTENPORT=8050
    - ORDERER_GENERAL_LOCALMSPID=OrdererMSP
    - ORDERER_GENERAL_LOCALMSPDIR=/var/hyperledger/orderer/msp
    # enabled TLS
    - ORDERER_GENERAL_TLS_ENABLED=true
    - ORDERER_GENERAL_TLS_PRIVATEKEY=/var/hyperledger/orderer/tls/server.key
    - ORDERER_GENERAL_TLS_CERTIFICATE=/var/hyperledger/orderer/tls/server.crt
    - ORDERER_GENERAL_TLS_ROOTCAS=[/var/hyperledger/orderer/tls/ca.crt]
    - ORDERER_GENERAL_CLUSTER_CLIENTCERTIFICATE=/var/hyperledger/orderer/tls/server.crt
    - ORDERER_GENERAL_CLUSTER_CLIENTPRIVATEKEY=/var/hyperledger/orderer/tls/server.key
    - ORDERER_GENERAL_CLUSTER_ROOTCAS=[/var/hyperledger/orderer/tls/ca.crt]
    - ORDERER_GENERAL_BOOTSTRAPMETHOD=none
    - ORDERER_CHANNELPARTICIPATION_ENABLED=true
    - ORDERER_ADMIN_TLS_ENABLED=true
    - ORDERER_ADMIN_TLS_CERTIFICATE=/var/hyperledger/orderer/tls/server.crt
    - ORDERER_ADMIN_TLS_PRIVATEKEY=/var/hyperledger/orderer/tls/server.key
    - ORDERER_ADMIN_TLS_ROOTCAS=[/var/hyperledger/orderer/tls/ca.crt]
    - ORDERER_ADMIN_TLS_CLIENTROOTCAS=[/var/hyperledger/orderer/tls/ca.crt]
    # orderer admin listenaddress
    - ORDERER_ADMIN_LISTENADDRESS=0.0.0.0:8053
    # orderer oeprations listenAddress - 0.0.0.0:9440 (Orderer)
    - ORDERER_OPERATIONS_LISTENADDRESS=0.0.0.0:9440
    - ORDERER_METRICS_PROVIDER=prometheus
  working_dir: /root
  command: orderer
  volumes:
      - ../organizations/ordererOrganizations/gov.my/orderers/orderer2.gov.my/msp:/var/hyperledger/orderer/msp
      - ../organizations/ordererOrganizations/gov.my/orderers/orderer2.gov.my/tls/:/var/hyperledger/orderer/tls
      - orderer2.gov.my:/var/hyperledger/production/orderer
  ports:
    - 8050:8050
    - 8053:8053
    - 9442:9440
  networks:
</code></pre>

