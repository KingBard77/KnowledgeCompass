# How to Add Peer in Hyperledger Fabric Version 2.5

##### blockchain

##### hyperledger

##### 1 September 2023

This guide is designed to assist you in adding a new peer to your existing Hyperledger Fabric v2.5 network. Follow the steps outlined here to ensure a smooth and error-free integration. 

![](960x640.png)
Diagram 1 shows the Blockchain network of current status

## Prerequisites

Before proceeding, ensure you have:

- **Hyperledger Fabric v2.5** installed and operational
- **Basic understanding of Blockchain and Hyperledger Fabric**
- **Administrative access to the Hyperledger network**

> **Important:** Confirm that your existing network is functioning properly before adding a new peer.

### Step 1: Initial Preparations

Prepare your environment and backup existing configurations.

- **Backup**: Always backup your existing configurations.
- **Environment Variables**: Set the required environment variables.

<pre><code class="language-bash">
# Backup existing configurations
cp /path/to/config /path/to/config.bak
# Set environment variables
export FABRIC_CFG_PATH=/path/to/config
</code></pre>

> **Info:** Store backups in multiple secure locations.

### Step 2: Update Configuration Files

Edit your `core.yaml` and `crypto-config.yaml` files to include the new peer's configurations.

<pre><code class="language-bash">
# Edit the core.yaml file
nano /path/to/core.yaml
# Edit the crypto-config.yaml file
nano /path/to/crypto-config.yaml
</code></pre>

> **Reminder:** Double-check your changes before saving the files.

### Step 3: Generate Certificates and Start the Peer

Execute the following commands to generate the necessary certificates and start the new peer.

<pre><code class="language-bash">
# Generate certificates for the new peer
cryptogen generate --config=./crypto-config-peer.yaml
# Start the new peer
peer node start
</code></pre>

> **Info:** Examine the logs to confirm that the peer has started without issues.

### Step 4: Verification

To confirm that your new peer is operational, run the following command.

![](960x640.png)
Diagram 2 shows the Blockchain network of current status

<pre><code class="language-bash">
# Check the list of peers in the network
peer node list
</code></pre>

> **Important:** Make sure the new peer appears in the list.

## Troubleshooting

If you face any issues, consider the following common problems and their solutions:

- **Issue 1**: *description and solution*
- **Issue 2**: *description and solution*

## Conclusion

Congratulations, you've successfully added a new peer to your Hyperledger Fabric v2.5 network!

> **Link:** For more details or advanced configurations, refer to the [official Hyperledger Fabric Documentation](https://hyperledger-fabric.readthedocs.io/).

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

