<!--
  - @copyright Copyright (c) 2021 Lyseon Tech <contato@lt.coop.br>
  -
  - @author Lyseon Tech <contato@lt.coop.br>
  - @author Vinicios Gomes <viniciusgomesvaian@gmail.com>
  -
  - @license GNU AGPL version 3 or any later version
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU Affero General Public License as
  - published by the Free Software Foundation, either version 3 of the
  - License, or (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU Affero General Public License for more details.
  -
  - You should have received a copy of the GNU Affero General Public License
  - along with this program.  If not, see <http://www.gnu.org/licenses/>.
  -
  -->

<template>
	<div id="formLibresign" class="form-libresign">
		<div class="form-group">
			<label for="hosts">{{ t('libresign', 'Email') }}</label>
			<input
				id="hosts"
				ref="hosts"
				v-model="signature.hosts"
				type="email"
				:disabled="updating">
		</div>
		<div class="form-group">
			<label for="commonName">{{ t('libresign', 'Nome (CN)') }}</label>
			<input
				id="commonName"
				ref="commonName"
				v-model="signature.commonName"
				type="text"
				:disabled="updating">
		</div>
		<div class="form-group">
			<label for="country">{{ t('libresign', 'País (C)') }}</label>
			<input
				id="country"
				ref="country"
				v-model="signature.country"
				type="text"
				:disabled="updating">
		</div>
		<div class="form-group">
			<label for="organization">{{ t('libresign', 'Organização (O)') }}</label>
			<input
				id="organization"
				ref="organization"
				v-model="signature.organization"
				type="text"
				:disabled="updating">
		</div>
		<div class="form-group">
			<label for="organizationUnit">{{ t('libresign', 'Unidade da organização (OU)') }}</label>
			<input
				id="organizationUnit"
				ref="organizationUnit"
				v-model="signature.organizationUnit"
				type="text"
				:disabled="updating">
		</div>
		<div class="form-group">
			<label for="password">{{ t('libresign', 'Senha da assinatura') }}</label>
			<input
				id="password"
				v-model="signature.password"
				type="text"
				:disabled="updating">
		</div>
		<div class="form-group">
			<label for="path">{{ t('libresign', 'Armazenamento da assinatura') }}</label>
			<div>
				<input
					id="path"
					ref="path"
					v-model="signature.path"
					type="text"
					:disabled="1">
				<button
					id="pickFromCloud"
					:class="'icon-folder'"
					:title="t('libresign', 'Selecionar pasta onde assinatura será salva')"
					:disabled="updating"
					@click.stop="pickFromCloud">
					{{ t('libresign', 'Selecionar Pasta') }}
				</button>
			</div>
		</div>
		<input
			type="button"
			class="primary"
			:value="t('libresign', 'Gerar Assinatura')"
			:disabled="updating || !savePossible"
			@click="saveSignature">
		<Modal
			v-if="modal"
			dark=""
			@close="closeModal">
			<div class="modal_content">
				{{ t('libresign','Assinatura gerada e disponivel em ') }} {{ signature.path }} !
			</div>
		</Modal>
	</div>
</template>

<script>
import '@nextcloud/dialogs/styles/toast.scss'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import { showError, getFilePickerBuilder } from '@nextcloud/dialogs'
import Modal from '@nextcloud/vue/dist/Components/Modal'
import { translate as t } from '@nextcloud/l10n'
export default {
	name: 'FormLibresign',
	components: {
		Modal,
	},
	data() {
		return {
			signature: {
				commonName: '',
				hosts: '',
				country: '',
				organization: '',
				organizationUnit: '',
				path: '',
				password: '',
			},
			updating: false,
			loading: true,
			modal: false,
		}
	},
	computed: {
		savePossible() {
			return (
				this.signature
                && this.signature.commonName !== ''
                && this.signature.hosts !== ''
                && this.signature.country !== ''
                && this.signature.organization !== ''
                && this.signature.organizationUnit !== ''
                && this.signature.password !== ''
                && this.signature.path !== ''
			)
		},
	},
	async mounted() {
		this.loading = false
		this.$refs.hosts.focus()
	},

	methods: {
		async saveSignature() {
			this.updating = true
			try {
				const response = await axios.post(
					generateUrl('/apps/libresign/api/0.1/signature/generate'),
					this.signature
				)
				if (!response.data || !response.data.signature) {
					throw new Error(response.data)
				}
				this.signature.path = response.data.signature
				this.showModal()
			} catch (e) {
				console.error(e)
				showError(t('libresign', 'Não foi possivel criar assinatura'))
			}
			this.updating = false
		},
		showModal() {
			this.modal = true
		},
		closeModal() {
			this.modal = false
			this.signature = {
				commonName: '',
				hosts: '',
				country: '',
				organization: '',
				organizationUnit: '',
				path: '',
				password: '',
			}
		},
		pickFromCloud() {
			const picker = getFilePickerBuilder(t('libresign', 'Escolha uma pasta para armazenar a assinatura'))
				.setMultiSelect(false)
				.addMimeTypeFilter('httpd/unix-directory')
				.setModal(true)
				.setType(1)
				.allowDirectories(true)
				.build()

			picker.pick().then((path) => {
				if (!path) {
					path = '/'
				}
				this.signature.path = path
			})
		},
	},
}
</script>
<style>
#formLibresign{
	width: 60%;
	text-align: left;
	margin: 20px;
}

.form-group > input[type='text'],
.form-group > input[type='email'] {
	width: 100%;
}

#path {
	width: 80%;
}

#pickFromCloud{
	display: inline-block;
	margin: 16px;
	background-position: 16px center;
	padding: 12px;
	padding-left: 44px;
}

.modal_content {
	text-align: center;
	margin: 40px;
}
</style>
